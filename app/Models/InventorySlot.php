<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class InventorySlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'slot_date',
        'start_time',
        'end_time',
        'capacity',
        'booked_quantity',
        'cutoff_minutes',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'slot_date' => 'date',
        'capacity' => 'integer',
        'booked_quantity' => 'integer',
        'cutoff_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getRemainingCapacityAttribute(): int
    {
        return max(0, (int) $this->capacity - (int) $this->booked_quantity);
    }

    public function getSlotDateTimeAttribute(): Carbon
    {
        return Carbon::parse($this->slot_date->toDateString() . ' ' . $this->start_time);
    }

    public function getCutoffDateTimeAttribute(): Carbon
    {
        return $this->slot_date_time->copy()->subMinutes(max(0, (int) $this->cutoff_minutes));
    }

    public function isPastCutoff(): bool
    {
        return now()->greaterThanOrEqualTo($this->cutoff_date_time);
    }
}
