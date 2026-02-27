<?php

namespace App\Models;

use App\Models\DispatchAssignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'vendor_code',
        'contact_person',
        'email',
        'phone',
        'line_id',
        'whatsapp_number',
        'service_type',
        'address',
        'default_commission_rate',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'default_commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function (Vendor $vendor) {
            if (blank($vendor->vendor_code)) {
                $vendor->vendor_code = 'VDR-' . strtoupper(Str::random(6));
            }
        });
    }

    public function dispatchAssignments()
    {
        return $this->hasMany(DispatchAssignment::class);
    }
}
