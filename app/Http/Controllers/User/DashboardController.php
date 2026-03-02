<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\StripePaymentService;

class DashboardController extends Controller
{
    /**
     * Display user dashboard with overview
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'upcoming_bookings' => $user->bookings()
                ->where('service_date', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->count(),
            'points' => $user->points,
            'available_coupons' => $user->availableCoupons()->count(),
        ];

        $upcomingBookings = $user->bookings()
            ->where('service_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('service_date', 'asc')
            ->limit(3)
            ->get();

        $recentNotifications = $user->notifications()
            ->latest()
            ->limit(5)
            ->get();

        $unreadNotificationsCount = $user->unreadNotifications()->count();

        return view('user.dashboard.index', compact(
            'user',
            'stats',
            'upcomingBookings',
            'recentNotifications',
            'unreadNotificationsCount'
        ));
    }

    public function markNotificationRead(string $notificationId)
    {
        /** @var User $user */
        $user = Auth::user();

        $notification = $user
            ->notifications()
            ->whereKey($notificationId)
            ->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back();
    }

    public function markAllNotificationsRead()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return back();
    }

    /**
     * Display user's booking history
     */
    public function bookings(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $status = $request->get('status', 'all');

        $query = $user->bookings()->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(10);

        return view('user.dashboard.bookings', compact('bookings', 'status'));
    }

    /**
     * Show booking details
     */
    public function showBooking($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $booking = $user->bookings()->with(['product', 'review'])->findOrFail($id);

        return view('user.dashboard.booking-detail', compact('booking'));
    }

    /**
     * Cancel booking request
     */
    public function cancelBooking(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $booking = $user->bookings()->findOrFail($id);

        // Check if cancellation is allowed
        if ($booking->status === 'cancelled' || $booking->status === 'completed') {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        // Check cancellation policy (24 hours before)
        $hoursUntilService = now()->diffInHours($booking->service_date, false);

        if ($hoursUntilService < 24) {
            return back()->with('error', 'Cancellation is only allowed 24 hours before the service date.');
        }

        if ($booking->payment_status === 'paid' && filled($booking->stripe_payment_intent_id)) {
            try {
                app(StripePaymentService::class)->refundBooking($booking);
            } catch (\Throwable $exception) {
                report($exception);

                return back()->with('error', 'Unable to process Stripe refund right now. Please contact support.');
            }
        }

        $booking->update([
            'status' => 'cancelled',
            'payment_status' => $booking->payment_status === 'paid' ? 'refunded' : $booking->payment_status,
            'refunded_at' => $booking->payment_status === 'paid' ? now() : $booking->refunded_at,
            'refund_amount' => $booking->payment_status === 'paid' ? $booking->total_price : $booking->refund_amount,
            'admin_notes' => 'Cancelled by user at ' . now()->format('Y-m-d H:i:s')
        ]);

        return back()->with('success', 'Booking cancelled successfully. Refund will be processed within 3-5 business days.');
    }

    /**
     * Display user's points and history
     */
    public function points()
    {
        /** @var User $user */
        $user = Auth::user();

        $pointHistory = $user->pointLedgers()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $expiringPoints = $user->pointLedgers()
            ->where('type', 'earned')
            ->where('expires_at', '<=', now()->addMonths(3))
            ->where('expires_at', '>', now())
            ->sum('points');

        return view('user.dashboard.points', compact('pointHistory', 'expiringPoints'));
    }

    /**
     * Display user's coupons
     */
    public function coupons()
    {
        /** @var User $user */
        $user = Auth::user();

        $availableCoupons = $user->availableCoupons()->get();

        $usedCoupons = $user->coupons()
            ->wherePivot('usage_count', '>', 0)
            ->orderBy('user_coupons.last_used_at', 'desc')
            ->get();

        return view('user.dashboard.coupons', compact('availableCoupons', 'usedCoupons'));
    }

    /**
     * Display user profile settings
     */
    public function profile()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('user.dashboard.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }
}
