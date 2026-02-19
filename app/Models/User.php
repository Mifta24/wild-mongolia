<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'points',
        'lifetime_points',
        'membership_tier',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'points' => 'integer',
            'lifetime_points' => 'integer',
            'membership_tier' => 'string',
        ];
    }

    public function supportConversation(): HasOne
    {
        return $this->hasOne(SupportConversation::class);
    }

    public function supportMessages(): HasMany
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function pointLedgers(): HasMany
    {
        return $this->hasMany(PointLedger::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupons')
            ->withPivot('usage_count', 'last_used_at')
            ->withTimestamps();
    }

    /**
     * Get available coupons for user
     */
    public function availableCoupons()
    {
        return $this->coupons()
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->whereRaw('(user_coupons.usage_count < coupons.usage_per_user OR coupons.usage_per_user IS NULL)');
    }

    /**
     * Add points to user with ledger entry
     */
    public function addPoints(int $points, string $source, ?int $bookingId = null, ?string $description = null)
    {
        $this->increment('points', $points);

        // Track lifetime points for earned points
        if ($source === 'booking' || $source === 'admin_adjustment' || $source === 'refunded') {
            $this->increment('lifetime_points', $points);
        }

        $this->refresh();

        return $this->pointLedgers()->create([
            'type' => 'earned',
            'points' => $points,
            'balance_after' => $this->points,
            'source' => $source,
            'booking_id' => $bookingId,
            'description' => $description,
            'expires_at' => now()->addYear(),
        ]);
    }

    /**
     * Deduct points from user with ledger entry
     */
    public function deductPoints(int $points, string $source, ?int $bookingId = null, ?string $description = null)
    {
        if ($this->points < $points) {
            throw new \Exception('Insufficient points');
        }

        $this->decrement('points', $points);
        $this->refresh();

        return $this->pointLedgers()->create([
            'type' => 'used',
            'points' => -$points,
            'balance_after' => $this->points,
            'source' => $source,
            'booking_id' => $bookingId,
            'description' => $description,
        ]);
    }
}
