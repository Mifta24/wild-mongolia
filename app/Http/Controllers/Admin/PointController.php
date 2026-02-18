<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PointLedger;
use App\Services\PointService;
use Illuminate\Http\Request;

class PointController extends Controller
{
    protected $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

    /**
     * Display points overview
     */
    public function index(Request $request)
    {
        $query = PointLedger::with('user')->latest();

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $transactions = $query->paginate(20);

        // Statistics
        $stats = [
            'total_points_issued' => PointLedger::where('type', 'earned')->sum('points'),
            'total_points_used' => abs(PointLedger::where('type', 'used')->sum('points')),
            'total_points_expired' => abs(PointLedger::where('type', 'expired')->sum('points')),
            'total_active_balance' => User::sum('points'),
        ];

        return view('admin.points.index', compact('transactions', 'stats'));
    }

    /**
     * Show form to manually adjust user points
     */
    public function adjustForm(User $user)
    {
        $summary = $this->pointService->getUserPointSummary($user);
        return view('admin.points.adjust', compact('user', 'summary'));
    }

    /**
     * Manually adjust user points
     */
    public function adjust(Request $request, User $user)
    {
        $validated = $request->validate([
            'points' => 'required|integer|not_in:0',
            'description' => 'required|string|max:255',
        ]);

        try {
            if ($validated['points'] > 0) {
                $user->addPoints(
                    $validated['points'],
                    'admin_adjustment',
                    null,
                    $validated['description']
                );
                $message = "Added {$validated['points']} points to {$user->name}";
            } else {
                $user->deductPoints(
                    abs($validated['points']),
                    'admin_adjustment',
                    null,
                    $validated['description']
                );
                $message = "Deducted " . abs($validated['points']) . " points from {$user->name}";
            }

            // Update membership tier
            $this->pointService->updateMembershipTier($user);

            return redirect()
                ->route('admin.points.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Manually expire old points
     */
    public function expirePoints()
    {
        $count = $this->pointService->expirePoints();

        return back()->with('success', "Expired points for {$count} entries");
    }

    /**
     * Show membership tier statistics
     */
    public function membershipStats()
    {
        $stats = [
            'silver' => User::where('membership_tier', 'silver')->count(),
            'gold' => User::where('membership_tier', 'gold')->count(),
            'platinum' => User::where('membership_tier', 'platinum')->count(),
        ];

        $topMembers = User::orderBy('points', 'desc')
            ->take(10)
            ->get();

        return view('admin.points.membership-stats', compact('stats', 'topMembers'));
    }
}
