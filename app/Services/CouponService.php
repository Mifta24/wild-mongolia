<?php

namespace App\Services;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CouponService
{
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
        string $serviceType = null
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
}
