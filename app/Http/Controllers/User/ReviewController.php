<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, int $bookingId): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking = Booking::query()
            ->where('user_id', Auth::id())
            ->with('review')
            ->findOrFail($bookingId);

        if ($booking->status !== 'completed') {
            return back()->with('error', 'Review can only be submitted after service is completed.');
        }

        if (!$booking->product_id || !$booking->product) {
            return back()->with('error', 'This booking has no linked product for review.');
        }

        if ($booking->review && $booking->review->status === 'approved') {
            return back()->with('error', 'This booking already has an approved review.');
        }

        Review::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id' => Auth::id(),
                'product_id' => $booking->product_id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'status' => 'pending',
                'moderation_notes' => null,
                'moderated_by' => null,
                'moderated_at' => null,
            ]
        );

        return back()->with('success', 'Thank you! Your review was submitted and is pending moderation.');
    }
}
