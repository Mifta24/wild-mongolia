<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Vendor;
use App\Services\BookingEmailService;
use App\Services\StripePaymentService;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Booking::query()->with('user'); // Eager load user untuk performa

        // Filter by Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Search by Booking Code / User Name
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('meeting_point') && in_array($request->meeting_point, ['yes', 'no'], true)) {
            $query->where('service_type', 'tour')
                ->where('meeting_point_confirmed', $request->meeting_point === 'yes');
        }

        // Ambil data terbaru dulu, paginate 10 per halaman
        $bookings = $query->latest()->paginate(10)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = Booking::with(['user', 'dispatchAssignment.vendor'])->findOrFail($id);
        $vendors = Vendor::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'default_commission_rate']);

        return view('admin.bookings.show', compact('booking', 'vendors'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string' // Opsional: Catatan admin
        ]);

        $booking = Booking::findOrFail($id);

        if ($request->status === 'completed' && $booking->payment_status !== 'paid') {
            return redirect()->route('admin.bookings.show', $id)
                ->with('error', 'Booking can only be completed after payment status is paid.');
        }

        $booking->update([
            'status' => $request->status,
            // 'admin_notes' => $request->notes, // Jika ada kolom ini
        ]);

        // TODO: Kirim notifikasi email ke user di sini jika status berubah

        return redirect()->route('admin.bookings.show', $id)
            ->with('success', 'Booking status updated successfully.');
    }

    public function refund(string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->payment_status !== 'paid') {
            return back()->with('error', 'Only paid bookings can be refunded.');
        }

        if (blank($booking->stripe_payment_intent_id)) {
            return back()->with('error', 'Stripe payment reference is missing for this booking.');
        }

        try {
            app(StripePaymentService::class)->refundBooking($booking);

            $booking->update([
                'payment_status' => 'refunded',
                'refunded_at' => now(),
                'refund_amount' => $booking->total_price,
                'admin_notes' => trim(($booking->admin_notes ? $booking->admin_notes . PHP_EOL : '') . 'Refunded by admin at ' . now()->format('Y-m-d H:i:s')),
            ]);

            return back()->with('success', 'Stripe refund has been initiated successfully.');
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Failed to process Stripe refund. Please try again.');
        }
    }

    public function resendConfirmationEmail(string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->payment_status !== 'paid') {
            return back()->with('error', 'Confirmation email can only be resent for paid bookings.');
        }

        if (blank($booking->guest_email)) {
            return back()->with('error', 'Guest email is empty for this booking.');
        }

        try {
            app(BookingEmailService::class)->resendConfirmation($booking);

            return back()->with('success', 'Confirmation email has been resent successfully.');
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Failed to resend confirmation email. Please try again.');
        }
    }

    public function showCheckInByToken(string $token)
    {
        $booking = Booking::query()
            ->with(['user', 'checkedInBy'])
            ->where('voucher_token', $token)
            ->first();

        if (!$booking) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Invalid or expired voucher token.');
        }

        return view('admin.bookings.checkin', [
            'booking' => $booking,
            'isEligible' => $this->isCheckInEligible($booking),
        ]);
    }

    public function processCheckInByToken(string $token)
    {
        $booking = Booking::query()
            ->where('voucher_token', $token)
            ->first();

        if (!$booking) {
            return back()->with('error', 'Invalid or expired voucher token.');
        }

        if (!$this->isCheckInEligible($booking)) {
            return back()->with('error', 'This voucher is not eligible for check-in.');
        }

        if ($booking->checked_in_at) {
            return back()->with('success', 'Voucher has already been checked in.');
        }

        $booking->update([
            'checked_in_at' => now(),
            'checked_in_by' => Auth::id(),
            'status' => $booking->status === 'pending' ? 'confirmed' : $booking->status,
            'admin_notes' => trim(($booking->admin_notes ? $booking->admin_notes . PHP_EOL : '') . 'Checked-in via QR by user ID ' . Auth::id() . ' at ' . now()->format('Y-m-d H:i:s')),
        ]);

        return back()->with('success', 'QR check-in completed successfully.');
    }

    private function isCheckInEligible(Booking $booking): bool
    {
        if ($booking->payment_status !== 'paid') {
            return false;
        }

        if ($booking->status === 'cancelled') {
            return false;
        }

        if (!$this->isWithinCheckInWindow($booking->service_date)) {
            return false;
        }

        return !blank($booking->voucher_token);
    }

    private function isWithinCheckInWindow(?CarbonInterface $serviceDate): bool
    {
        if (!$serviceDate) {
            return false;
        }

        $daysBefore = max(0, (int) Cache::get(
            'booking.check_in_window.days_before',
            config('booking.check_in_window.days_before', 1)
        ));
        $daysAfter = max(0, (int) Cache::get(
            'booking.check_in_window.days_after',
            config('booking.check_in_window.days_after', 1)
        ));

        $today = now()->startOfDay();
        $windowStart = $today->copy()->subDays($daysBefore);
        $windowEnd = $today->copy()->addDays($daysAfter);

        return $serviceDate->copy()->startOfDay()->betweenIncluded($windowStart, $windowEnd);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
