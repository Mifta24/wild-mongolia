<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_code',
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'service_type',
        'service_subtype',
        'destination',
        'experience_type',
        'product_id',
        'product_name',
        'service_date',
        'service_time',
        'pickup_location',
        'dropoff_location',
        'flight_number',
        'quantity',
        'adult_pax',
        'child_pax',
        'total_price',
        'currency',
        'status',
        'payment_status',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'stripe_receipt_url',
        'paid_at',
        'refunded_at',
        'refund_amount',
        'invoice_number',
        'special_request',
        'admin_notes',
    ];

    /**
     * Casting tipe data otomatis
     */
    protected $casts = [
        'service_date' => 'date',
        'total_price' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // Jika nanti ada tabel Products terpisah (Polymorphic relationship opsional)
    // public function product() { ... }

    /**
     * Logic Otomatis saat Booking dibuat
     */
    protected static function booted()
    {
        static::creating(function ($booking) {
            // Generate Booking Code: TRV-TAHUNBULANTANGGAL-RANDOM
            // Contoh: TRV-20260119-A1B2
            $booking->booking_code = 'TRV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        });
    }
}
