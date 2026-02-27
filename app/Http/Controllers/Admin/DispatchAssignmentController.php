<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DispatchAssignment;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatchAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = DispatchAssignment::query()
            ->with(['booking', 'vendor'])
            ->latest();

        if ($request->filled('dispatch_status')) {
            $query->where('dispatch_status', $request->dispatch_status);
        }

        if ($request->filled('settlement_status')) {
            $query->where('settlement_status', $request->settlement_status);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('driver_name', 'like', "%{$search}%")
                    ->orWhereHas('booking', function ($bookingQuery) use ($search) {
                        $bookingQuery->where('booking_code', 'like', "%{$search}%")
                            ->orWhere('guest_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                        $vendorQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $assignments = $query->paginate(15)->withQueryString();
        $vendors = Vendor::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.dispatch-assignments.index', compact('assignments', 'vendors'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        $booking = Booking::query()->findOrFail($validated['booking_id']);

        if ($validated['settlement_status'] === 'paid' && $validated['dispatch_status'] !== 'completed') {
            return redirect()->back()->with('error', 'Settlement can be marked paid only after dispatch is completed.');
        }

        if ($validated['dispatch_status'] === 'completed' && $booking->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Dispatch can only be completed when booking payment status is paid.');
        }

        $assignment = DispatchAssignment::query()->updateOrCreate(
            ['booking_id' => $booking->id],
            $this->buildPayload($validated, $booking, true)
        );

        return redirect()->back()->with('success', "Dispatch assignment saved for booking {$booking->booking_code}.");
    }

    public function update(Request $request, DispatchAssignment $dispatchAssignment)
    {
        $validated = $this->validatePayload($request, true);

        if ($validated['settlement_status'] === 'paid' && $validated['dispatch_status'] !== 'completed') {
            return redirect()->back()->with('error', 'Settlement can be marked paid only after dispatch is completed.');
        }

        if ($validated['dispatch_status'] === 'completed' && $dispatchAssignment->booking->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Dispatch can only be completed when booking payment status is paid.');
        }

        $dispatchAssignment->update(
            $this->buildPayload($validated, $dispatchAssignment->booking, false)
        );

        return redirect()->back()->with('success', 'Dispatch assignment updated successfully.');
    }

    private function validatePayload(Request $request, bool $isUpdate = false): array
    {
        $bookingIdRule = $isUpdate ? ['nullable', 'exists:bookings,id'] : ['required', 'exists:bookings,id'];

        return $request->validate([
            'booking_id' => $bookingIdRule,
            'vendor_id' => ['required', 'exists:vendors,id'],
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_phone' => ['nullable', 'string', 'max:50'],
            'vehicle_plate' => ['nullable', 'string', 'max:50'],
            'dispatch_status' => ['required', 'in:pending,assigned,on_route,completed,cancelled'],
            'dispatch_notes' => ['nullable', 'string'],
            'commission_type' => ['required', 'in:percentage,fixed'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'commission_flat_amount' => ['nullable', 'numeric', 'min:0'],
            'settlement_status' => ['required', 'in:unpaid,partially_paid,paid'],
            'settlement_notes' => ['nullable', 'string'],
        ]);
    }

    private function buildPayload(array $validated, Booking $booking, bool $isCreate): array
    {
        $commissionAmount = $this->calculateCommission($validated, (float) $booking->total_price);
        $vendorPayoutAmount = max((float) $booking->total_price - $commissionAmount, 0);

        $payload = [
            'vendor_id' => $validated['vendor_id'],
            'assigned_by' => Auth::id(),
            'driver_name' => $validated['driver_name'],
            'driver_phone' => $validated['driver_phone'] ?? null,
            'vehicle_plate' => $validated['vehicle_plate'] ?? null,
            'dispatch_status' => $validated['dispatch_status'],
            'dispatch_notes' => $validated['dispatch_notes'] ?? null,
            'commission_type' => $validated['commission_type'],
            'commission_rate' => $validated['commission_type'] === 'percentage' ? ($validated['commission_rate'] ?? 0) : null,
            'commission_flat_amount' => $validated['commission_type'] === 'fixed' ? ($validated['commission_flat_amount'] ?? 0) : null,
            'commission_amount' => round($commissionAmount, 2),
            'vendor_payout_amount' => round($vendorPayoutAmount, 2),
            'settlement_status' => $validated['settlement_status'],
            'settled_at' => $validated['settlement_status'] === 'paid' ? now() : null,
            'settlement_notes' => $validated['settlement_notes'] ?? null,
        ];

        if ($isCreate) {
            $payload['assigned_at'] = now();
        }

        return $payload;
    }

    private function calculateCommission(array $validated, float $bookingTotal): float
    {
        if (($validated['commission_type'] ?? 'percentage') === 'fixed') {
            return (float) ($validated['commission_flat_amount'] ?? 0);
        }

        $rate = (float) ($validated['commission_rate'] ?? 0);

        return ($bookingTotal * $rate) / 100;
    }
}
