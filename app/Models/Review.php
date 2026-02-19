<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'booking_id',
        'rating',
        'comment',
        'status',
        'moderation_notes',
        'moderated_by',
        'moderated_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'moderated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function (Review $review) {
            $review->refreshProductRating();
        });

        static::deleted(function (Review $review) {
            $review->refreshProductRating();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function refreshProductRating(): void
    {
        if (!$this->product_id) {
            return;
        }

        $aggregates = static::query()
            ->where('product_id', $this->product_id)
            ->where('status', 'approved')
            ->selectRaw('COUNT(*) as total_reviews, COALESCE(AVG(rating), 0) as average_rating')
            ->first();

        Product::whereKey($this->product_id)->update([
            'total_reviews' => (int) ($aggregates->total_reviews ?? 0),
            'average_rating' => round((float) ($aggregates->average_rating ?? 0), 2),
        ]);
    }
}
