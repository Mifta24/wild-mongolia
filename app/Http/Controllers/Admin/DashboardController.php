<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung Statistik Sederhana
        $totalRevenue = Booking::where('payment_status', 'paid')->sum('total_price');
        $newBookings = Booking::where('status', 'pending')->count();
        $todaysBookings = Booking::whereDate('created_at', today())->count();
        $totalMembers = User::count();
        $deactivatedAccounts = User::role('user')->whereNotNull('deactivated_at')->count();

        $deletionThreshold = now(config('app.timezone'))->addDays(7);
        $accountsPendingDeletionSoon = User::role('user')
            ->whereNotNull('deactivated_at')
            ->whereNotNull('scheduled_for_deletion_at')
            ->where('scheduled_for_deletion_at', '<=', $deletionThreshold)
            ->count();

        $upcomingDeactivatedUsers = User::role('user')
            ->whereNotNull('deactivated_at')
            ->whereNotNull('scheduled_for_deletion_at')
            ->where('scheduled_for_deletion_at', '<=', $deletionThreshold)
            ->orderBy('scheduled_for_deletion_at')
            ->take(5)
            ->get(['id', 'name', 'email', 'scheduled_for_deletion_at']);

        // Ambil 5 booking terbaru untuk widget "Recent Activity"
        $recentBookings = Booking::with('user')->latest()->take(5)->get();

        return view('admin.dashboard.index', compact(
            'totalRevenue',
            'newBookings',
            'todaysBookings',
            'totalMembers',
            'deactivatedAccounts',
            'accountsPendingDeletionSoon',
            'upcomingDeactivatedUsers',
            'recentBookings'
        ));
    }
}
