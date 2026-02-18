<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME2026',
                'name' => 'Welcome Discount',
                'description' => 'New customer welcome discount - 300 THB off',
                'type' => 'fixed',
                'value' => 300,
                'min_purchase' => 1000,
                'max_discount' => null,
                'applicable_to' => 'all',
                'usage_limit' => 100,
                'usage_per_user' => 1,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'CARTRIP10',
                'name' => '10% Off Car Rental',
                'description' => '10% discount on all car rental services',
                'type' => 'percentage',
                'value' => 10,
                'min_purchase' => 2000,
                'max_discount' => 500,
                'applicable_to' => 'car',
                'usage_limit' => null,
                'usage_per_user' => 2,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'TOUR15OFF',
                'name' => '15% Off Tours',
                'description' => '15% discount on all tour packages',
                'type' => 'percentage',
                'value' => 15,
                'min_purchase' => 3000,
                'max_discount' => 1000,
                'applicable_to' => 'tour',
                'usage_limit' => null,
                'usage_per_user' => 2,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'SUMMER500',
                'name' => 'Summer Special',
                'description' => 'Summer special - 500 THB flat discount',
                'type' => 'fixed',
                'value' => 500,
                'min_purchase' => 5000,
                'max_discount' => null,
                'applicable_to' => 'all',
                'usage_limit' => 50,
                'usage_per_user' => 1,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'VIP20',
                'name' => 'VIP 20% Discount',
                'description' => 'Exclusive 20% discount for VIP members',
                'type' => 'percentage',
                'value' => 20,
                'min_purchase' => 10000,
                'max_discount' => 3000,
                'applicable_to' => 'all',
                'usage_limit' => 20,
                'usage_per_user' => 1,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonth(),
                'is_active' => true,
            ],
            [
                'code' => 'FIRSTRIDE',
                'name' => 'First Ride Free',
                'description' => 'Get 1000 THB off your first booking',
                'type' => 'fixed',
                'value' => 1000,
                'min_purchase' => 2000,
                'max_discount' => null,
                'applicable_to' => 'all',
                'usage_limit' => 200,
                'usage_per_user' => 1,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addYear(),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
