<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookingSettingsController extends Controller
{
    public function editCheckInWindow()
    {
        return view('admin.settings.checkin-window', [
            'daysBefore' => (int) Cache::get(
                'booking.check_in_window.days_before',
                config('booking.check_in_window.days_before', 1)
            ),
            'daysAfter' => (int) Cache::get(
                'booking.check_in_window.days_after',
                config('booking.check_in_window.days_after', 1)
            ),
        ]);
    }

    public function updateCheckInWindow(Request $request)
    {
        $validated = $request->validate([
            'days_before' => 'required|integer|min:0|max:30',
            'days_after' => 'required|integer|min:0|max:30',
        ]);

        Cache::forever('booking.check_in_window.days_before', (int) $validated['days_before']);
        Cache::forever('booking.check_in_window.days_after', (int) $validated['days_after']);

        return back()->with('success', 'Check-in window settings updated successfully.');
    }

    public function resetCheckInWindow()
    {
        Cache::forget('booking.check_in_window.days_before');
        Cache::forget('booking.check_in_window.days_after');

        return back()->with('success', 'Check-in window settings reset to default values.');
    }
}
