<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'image_url',
        'car_model',
        'max_passengers',
        'max_luggage',
        'destination',
        'duration',
        'includes_lunch',
        'includes_pickup',
        'base_price',
        'discounted_price',
        'currency',
        'is_active',
        'is_featured',
        'total_reviews',
        'average_rating',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'includes_lunch' => 'boolean',
        'includes_pickup' => 'boolean',
    ];

    /**
     * Scope untuk filter berdasarkan tipe
     */
    public function scopeCars($query)
    {
        return $query->where('type', 'car')->where('is_active', true);
    }

    public function scopeTours($query)
    {
        return $query->where('type', 'tour')->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    /**
     * Get price (dengan diskon jika ada)
     */
    public function getFinalPriceAttribute()
    {
        return $this->discounted_price ?? $this->base_price;
    }

    /**
     * Relasi ke bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
