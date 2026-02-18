<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class CouponPolicy
{
    /**
     * Determine if the user can view any coupons
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view available coupons
    }

    /**
     * Determine if the user can view the coupon
     */
    public function view(User $user, Coupon $coupon): bool
    {
        return $user->hasRole('admin') || $coupon->is_active;
    }

    /**
     * Determine if the user can create coupons
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the coupon
     */
    public function update(User $user, Coupon $coupon): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the coupon
     */
    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can use the coupon
     */
    public function use(User $user, Coupon $coupon): bool
    {
        if (!$coupon->isValid()) {
            return false;
        }

        // Check if user has already maxed out their usage
        $userCoupon = DB::table('user_coupons')
            ->where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->first();

        if (!$userCoupon) {
            return true;
        }

        return $userCoupon->usage_count < $coupon->usage_per_user;
    }
}
