<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::query()->with(['user', 'product', 'booking', 'moderator']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($rating = $request->get('rating')) {
            $query->where('rating', (int) $rating);
        }

        if ($keyword = trim((string) $request->get('search'))) {
            $query->where(function ($innerQuery) use ($keyword) {
                $innerQuery->whereHas('user', function ($userQuery) use ($keyword) {
                    $userQuery->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                })->orWhereHas('product', function ($productQuery) use ($keyword) {
                    $productQuery->where('name', 'like', '%' . $keyword . '%');
                })->orWhere('comment', 'like', '%' . $keyword . '%');
            });
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function moderate(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'moderation_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $review->update([
            'status' => $validated['status'],
            'moderation_notes' => $validated['moderation_notes'] ?? null,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        return back()->with('success', 'Review moderation updated successfully.');
    }
}
