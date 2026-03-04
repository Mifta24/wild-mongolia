<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\MembershipTier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Carbon\Carbon;
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
        'google_id',
        'email_verified_at',
        'phone',
        'points',
        'lifetime_points',
        'membership_tier',
        'membership_started_at',
        'membership_expires_at',
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
            'membership_started_at' => 'datetime',
            'membership_expires_at' => 'datetime',
        ];
    }

    public function membershipTier(): MembershipTier
    {
        return MembershipTier::from($this->membership_tier ?? MembershipTier::SILVER->value);
    }

    public function hasActiveMembership(): bool
    {
        $tier = $this->membershipTier();

        if (!$tier->isPaidPlan()) {
            return true;
        }

        if (is_null($this->membership_expires_at)) {
            return false;
        }

        return $this->membership_expires_at->isFuture();
    }

    public function syncMembershipStatus(): void
    {
        $tier = $this->membershipTier();

        if (!$tier->isPaidPlan()) {
            return;
        }

        if ($this->hasActiveMembership()) {
            return;
        }

        $this->forceFill([
            'membership_tier' => MembershipTier::SILVER->value,
            'membership_started_at' => null,
            'membership_expires_at' => null,
        ])->save();
    }

    public function activateMembership(MembershipTier $tier, ?Carbon $startsAt = null): void
    {
        $startsAt ??= now(config('app.timezone'));

        $expiresAt = $tier->isPaidPlan()
            ? $startsAt->copy()->addYear()->endOfDay()
            : null;

        $this->forceFill([
            'membership_tier' => $tier->value,
            'membership_started_at' => $tier->isPaidPlan() ? $startsAt : null,
            'membership_expires_at' => $expiresAt,
        ])->save();
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
            'expires_at' => Carbon::now(config('app.timezone'))->endOfYear()->toDateString(),
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
