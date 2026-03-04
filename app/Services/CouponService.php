<?php

namespace App\Services;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CouponService
{
    public function issueWelcomeSignupCoupon(User $user): Coupon
    {
        return $this->issuePersonalCoupon($user, [
            'name' => 'Welcome Member Coupon',
            'description' => 'Welcome discount for new member registration.',
            'prefix' => 'WELCOME',
            'type' => 'fixed',
            'value' => 100,
            'min_purchase' => 1000,
            'max_discount' => null,
            'applicable_to' => 'all',
            'valid_days' => 30,
        ]);
    }

    public function issueGoldMembershipCoupon(User $user, string $action = 'subscribe'): Coupon
    {
        $isRenewal = $action === 'renew';

        return $this->issuePersonalCoupon($user, [
            'name' => $isRenewal ? 'Gold Renewal Bonus Coupon' : 'Gold Activation Bonus Coupon',
            'description' => $isRenewal
                ? 'Exclusive coupon for renewing Gold membership.'
                : 'Exclusive coupon for activating Gold membership.',
            'prefix' => $isRenewal ? 'GOLDRNW' : 'GOLDNEW',
            'type' => 'percentage',
            'value' => 10,
            'min_purchase' => 1000,
            'max_discount' => 500,
            'applicable_to' => 'all',
            'valid_days' => 45,
        ]);
    }
    public function issuePlatinumMembershipCoupon(User $user, string $action = 'subscribe'): Coupon
    {
        $isRenewal = $action === 'renew';

        return $this->issuePersonalCoupon($user, [
            'name' => $isRenewal ? 'Platinum Renewal Bonus Coupon' : 'Platinum Activation Bonus Coupon',
            'description' => $isRenewal
                ? 'Exclusive coupon for renewing Platinum membership.'
                : 'Exclusive coupon for activating Platinum membership.',
            'prefix' => $isRenewal ? 'PLATINUM_RNW' : 'PLATINUM_NEW',
            'type' => 'percentage',
            'value' => 15,
            'min_purchase' => 1000,
            'max_discount' => 750,
            'applicable_to' => 'all',
            'valid_days' => 45,
        ]);
    }

    /**
     * Validate if a coupon can be used
     */
    public function validateCoupon(
        string $code,
        User $user = null,
        float $orderAmount = 0,
        string $serviceType = null
    ): array {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Coupon not found'];
        }

        if (!$coupon->isValid()) {
            return ['valid' => false, 'message' => 'Coupon is not valid or has expired'];
        }

        // Check if user has already used this coupon
        if ($user) {
            $userUsage = DB::table('user_coupons')
                ->where('user_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->first();

            if ($userUsage && $userUsage->usage_count >= $coupon->usage_per_user) {
                return ['valid' => false, 'message' => 'You have already used this coupon the maximum number of times'];
            }
        }

        // Check minimum purchase requirement
        if ($coupon->min_purchase && $orderAmount < $coupon->min_purchase) {
            return [
                'valid' => false,
                'message' => "Minimum purchase of ฿{$coupon->min_purchase} required"
            ];
        }

        // Check if applicable to service type
        if ($coupon->applicable_to !== 'all') {
            if (!$serviceType || $serviceType !== $coupon->applicable_to) {
                return [
                    'valid' => false,
                    'message' => "This coupon is only applicable to {$coupon->applicable_to} services"
                ];
            }
        }

        $discount = $coupon->calculateDiscount($orderAmount);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Coupon is valid'
        ];
    }

    /**
     * Apply a coupon to a user
     */
    public function applyCoupon(Coupon $coupon, User $user, Booking $booking): void
    {
        DB::transaction(function () use ($coupon, $user, $booking) {
            // Increment coupon usage count
            $coupon->increment('usage_count');

            // Update or create user_coupon record
            $userCoupon = DB::table('user_coupons')
                ->where('user_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->first();

            if ($userCoupon) {
                DB::table('user_coupons')
                    ->where('user_id', $user->id)
                    ->where('coupon_id', $coupon->id)
                    ->update([
                        'usage_count' => DB::raw('usage_count + 1'),
                        'last_used_at' => now(),
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('user_coupons')->insert([
                    'user_id' => $user->id,
                    'coupon_id' => $coupon->id,
                    'usage_count' => 1,
                    'last_used_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    /**
     * Refund a coupon usage (when booking is cancelled)
     */
    public function refundCoupon(Coupon $coupon, User $user): void
    {
        DB::transaction(function () use ($coupon, $user) {
            // Decrement coupon usage count
            $coupon->decrement('usage_count');

            // Decrement user coupon usage count
            $userCoupon = DB::table('user_coupons')
                ->where('user_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->first();

            if ($userCoupon && $userCoupon->usage_count > 0) {
                DB::table('user_coupons')
                    ->where('user_id', $user->id)
                    ->where('coupon_id', $coupon->id)
                    ->update([
                        'usage_count' => DB::raw('usage_count - 1'),
                        'updated_at' => now(),
                    ]);
            }
        });
    }

    /**
     * Get available coupons for a user
     */
    public function getAvailableCoupons(
        User $user = null,
        float $orderAmount = 0,
        string $serviceType = null,
        string $source = null
    ): array {
        $query = Coupon::where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->where(function($q) use ($orderAmount) {
                $q->whereNull('min_purchase')
                  ->orWhere('min_purchase', '<=', $orderAmount);
            });

        // Filter by service type if provided
        if ($serviceType) {
            $query->where(function($q) use ($serviceType) {
                $q->where('applicable_to', 'all')
                  ->orWhere('applicable_to', $serviceType);
            });
        }

        if ($source && $source !== 'all') {
            $query->where(function ($q) use ($source) {
                if ($source === 'welcome') {
                    $q->where('code', 'like', 'WELCOME-%');
                    return;
                }

                if ($source === 'membership') {
                    $q->where('code', 'like', 'GOLDNEW-%')
                        ->orWhere('code', 'like', 'GOLDRNW-%')
                        ->orWhere('code', 'like', 'PLATINUM_NEW-%')
                        ->orWhere('code', 'like', 'PLATINUM_RNW-%');
                    return;
                }

                if ($source === 'general') {
                    $q->where('code', 'not like', 'WELCOME-%')
                        ->where('code', 'not like', 'GOLDNEW-%')
                        ->where('code', 'not like', 'GOLDRNW-%')
                        ->where('code', 'not like', 'PLATINUM_NEW-%')
                        ->where('code', 'not like', 'PLATINUM_RNW-%');
                }
            });
        }

        $coupons = $query->get();

        // Filter out coupons user has already maxed out
        if ($user) {
            $coupons = $coupons->filter(function ($coupon) use ($user) {
                $userUsage = DB::table('user_coupons')
                    ->where('user_id', $user->id)
                    ->where('coupon_id', $coupon->id)
                    ->first();

                if (!$userUsage) {
                    return true;
                }

                return $userUsage->usage_count < $coupon->usage_per_user;
            });
        }

        return $coupons->map(function ($coupon) use ($orderAmount) {
            return [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'source_label' => $this->resolveCouponSourceLabel((string) $coupon->code),
                'description' => $coupon->description,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'min_purchase' => $coupon->min_purchase,
                'max_discount' => $coupon->max_discount,
                'applicable_to' => $coupon->applicable_to,
                'valid_until' => $coupon->valid_until->format('Y-m-d'),
                'discount_preview' => $orderAmount > 0 ? $coupon->calculateDiscount($orderAmount) : null,
            ];
        })->values()->toArray();
    }

    /**
     * Create a new coupon
     */
    public function createCoupon(array $data): Coupon
    {
        return Coupon::create([
            'code' => strtoupper($data['code']),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'value' => $data['value'],
            'min_purchase' => $data['min_purchase'] ?? null,
            'max_discount' => $data['max_discount'] ?? null,
            'applicable_to' => $data['applicable_to'] ?? 'all',
            'usage_limit' => $data['usage_limit'] ?? null,
            'usage_per_user' => $data['usage_per_user'] ?? 1,
            'valid_from' => $data['valid_from'],
            'valid_until' => $data['valid_until'],
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Get user's coupon usage history
     */
    public function getUserCouponHistory(User $user): array
    {
        return DB::table('user_coupons')
            ->join('coupons', 'user_coupons.coupon_id', '=', 'coupons.id')
            ->where('user_coupons.user_id', $user->id)
            ->select([
                'coupons.code',
                'coupons.name',
                'user_coupons.usage_count',
                'user_coupons.last_used_at',
            ])
            ->orderBy('user_coupons.last_used_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Deactivate expired coupons
     */
    public function deactivateExpiredCoupons(): int
    {
        return Coupon::where('is_active', true)
            ->where('valid_until', '<', Carbon::today())
            ->update(['is_active' => false]);
    }

    private function issuePersonalCoupon(User $user, array $definition): Coupon
    {
        return DB::transaction(function () use ($user, $definition) {
            $validFrom = now(config('app.timezone'))->startOfDay();
            $validUntil = now(config('app.timezone'))->addDays((int) ($definition['valid_days'] ?? 30))->endOfDay();

            $coupon = Coupon::create([
                'code' => $this->generateUniqueCode((string) $definition['prefix']),
                'name' => (string) $definition['name'],
                'description' => (string) ($definition['description'] ?? ''),
                'type' => (string) $definition['type'],
                'value' => (float) $definition['value'],
                'min_purchase' => $definition['min_purchase'],
                'max_discount' => $definition['max_discount'],
                'applicable_to' => (string) ($definition['applicable_to'] ?? 'all'),
                'usage_limit' => 1,
                'usage_per_user' => 1,
                'valid_from' => $validFrom,
                'valid_until' => $validUntil,
                'is_active' => true,
            ]);

            $user->coupons()->syncWithoutDetaching([
                $coupon->id => [
                    'usage_count' => 0,
                    'last_used_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            return $coupon;
        });
    }

    private function generateUniqueCode(string $prefix): string
    {
        do {
            $code = strtoupper($prefix) . '-' . now(config('app.timezone'))->format('ymd') . '-' . Str::upper(Str::random(6));
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }

    private function resolveCouponSourceLabel(string $code): string
    {
        $normalized = strtoupper($code);

        return match (true) {
            str_starts_with($normalized, 'WELCOME-') => 'Welcome Bonus',
            str_starts_with($normalized, 'GOLDNEW-') => 'Gold Activation',
            str_starts_with($normalized, 'GOLDRNW-') => 'Gold Renewal',
            str_starts_with($normalized, 'PLATINUM_NEW-') => 'Platinum Activation',
            str_starts_with($normalized, 'PLATINUM_RNW-') => 'Platinum Renewal',
            default => 'General Coupon',
        };
    }
}
