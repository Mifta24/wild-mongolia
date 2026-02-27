<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'vendor_id',
        'assigned_by',
        'driver_name',
        'driver_phone',
        'vehicle_plate',
        'dispatch_status',
        'assigned_at',
        'dispatch_notes',
        'commission_type',
        'commission_rate',
        'commission_flat_amount',
        'commission_amount',
        'vendor_payout_amount',
        'settlement_status',
        'settled_at',
        'settlement_notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'settled_at' => 'datetime',
        'commission_rate' => 'decimal:2',
        'commission_flat_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'vendor_payout_amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
