<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class DashboardController extends Controller
{
    /**
     * Display user dashboard with overview
     */
    public function index()
    {
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

        return view('user.dashboard.index', compact('user', 'stats', 'upcomingBookings'));
    }

    /**
     * Display user's booking history
     */
    public function bookings(Request $request)
    {
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
        $user = Auth::user();
        $booking = $user->bookings()->with(['product', 'review'])->findOrFail($id);

        return view('user.dashboard.booking-detail', compact('booking'));
    }

    /**
     * Cancel booking request
     */
    public function cancelBooking(Request $request, $id)
    {
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

        $booking->update([
            'status' => 'cancelled',
            'admin_notes' => 'Cancelled by user at ' . now()->format('Y-m-d H:i:s')
        ]);

        // Refund points if used
        if ($booking->payment_status === 'paid') {
            $booking->update(['payment_status' => 'refunded']);
        }

        return back()->with('success', 'Booking cancelled successfully. Refund will be processed within 3-5 business days.');
    }

    /**
     * Display user's points and history
     */
    public function points()
    {
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
        $user = Auth::user();
        return view('user.dashboard.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
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
