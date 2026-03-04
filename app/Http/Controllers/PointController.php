<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointController extends Controller
{
    protected $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

    /**
     * Display user's points dashboard
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->syncMembershipStatus();
        $summary = $this->pointService->getUserPointSummary($user);

        $recentTransactions = $user->pointLedgers()
            ->with('booking')
            ->latest()
            ->take(10)
            ->get();

        return view('points.index', compact('summary', 'recentTransactions'));
    }

    /**
     * Show point history
     */
    public function history(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $query = $user->pointLedgers()
            ->with('booking')
            ->latest();

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->paginate(20);

        return view('points.history', compact('transactions'));
    }

    /**
     * Get point balance (API)
     */
    public function balance()
    {
        /** @var User $user */
        $user = Auth::user();
        $summary = $this->pointService->getUserPointSummary($user);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Calculate points for potential booking (API)
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Simulate a booking object to calculate points
        $booking = new \App\Models\Booking([
            'total_price' => $request->amount,
            'user_id' => $user->id,
        ]);
        $booking->user = $user;

        $points = $this->pointService->calculateBookingPoints($booking);
        $discount = $this->pointService->redeemPoints($user, min($user->points, 10000));

        return response()->json([
            'success' => true,
            'data' => [
                'points_to_earn' => $points,
                'current_balance' => $user->points,
                'max_redeemable_points' => min($user->points, (int)$request->amount),
                'discount_per_100_points' => 100,
                'tier_multiplier' => $user->membershipTier()->getPointMultiplier(),
            ],
        ]);
    }
}
