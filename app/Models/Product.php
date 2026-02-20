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
        'vehicle_type',
        'transmission',
        'is_air_conditioned',
        'year_manufactured',
        'max_passengers',
        'max_luggage',
        'destination',
        'duration',
        'category',
        'language',
        'includes_lunch',
        'includes_pickup',
        'base_price',
        'discounted_price',
        'distance_price_per_km',
        'minimum_distance_price',
        'currency',
        'is_active',
        'is_featured',
        'total_reviews',
        'average_rating',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'distance_price_per_km' => 'decimal:2',
        'minimum_distance_price' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'includes_lunch' => 'boolean',
        'includes_pickup' => 'boolean',
        'is_air_conditioned' => 'boolean',
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
     * Scope untuk filter berdasarkan vehicle type
     */
    public function scopeByVehicleType($query, $vehicleType)
    {
        if (!$vehicleType) {
            return $query;
        }
        return $query->where('vehicle_type', $vehicleType);
    }

    /**
     * Scope untuk filter berdasarkan price range
     */
    public function scopeByPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->whereRaw('COALESCE(discounted_price, base_price) >= ?', [$minPrice]);
        }
        if ($maxPrice) {
            $query->whereRaw('COALESCE(discounted_price, base_price) <= ?', [$maxPrice]);
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan duration (untuk tours)
     */
    public function scopeByDuration($query, $duration)
    {
        if (!$duration) {
            return $query;
        }
        return $query->where('duration', 'like', '%' . $duration . '%');
    }

    /**
     * Scope untuk filter berdasarkan category (untuk tours)
     */
    public function scopeByCategory($query, $category)
    {
        if (!$category) {
            return $query;
        }
        return $query->where('category', $category);
    }

    /**
     * Scope untuk filter berdasarkan language (untuk tours)
     */
    public function scopeByLanguage($query, $language)
    {
        if (!$language) {
            return $query;
        }
        return $query->where('language', $language);
    }

    /**
     * Scope untuk sorting
     */
    public function scopeSortBy($query, $sortType = 'featured')
    {
        return match ($sortType) {
            'price_asc' => $query->orderByRaw('COALESCE(discounted_price, base_price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(discounted_price, base_price) DESC'),
            'rating' => $query->orderByDesc('average_rating'),
            'popular' => $query->orderByDesc('total_reviews'),
            default => $query->orderByDesc('is_featured')->orderByDesc('average_rating'),
        };
    }

    /**
     * Calculate distance-based price for cars
     */
    public function calculateDistancePrice($kilometers)
    {
        if ($this->type !== 'car' || !$this->distance_price_per_km) {
            return $this->final_price;
        }

        $price = $this->distance_price_per_km * $kilometers;

        // Apply minimum distance price if set
        if ($this->minimum_distance_price && $price < $this->minimum_distance_price) {
            $price = $this->minimum_distance_price;
        }

        return round($price, 2);
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
