<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\DispatchAssignment;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VendorOperationsSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = collect([
            [
                'name' => 'Bangkok Transfer Co.',
                'vendor_code' => 'VDR-BKK001',
                'contact_person' => 'Somchai Prasert',
                'email' => 'ops@bangkoktransfer.example',
                'phone' => '+66-81-555-1001',
                'line_id' => 'bkktransfer',
                'service_type' => 'car',
                'default_commission_rate' => 12,
                'is_active' => true,
                'notes' => 'Airport and city transfer specialist',
            ],
            [
                'name' => 'Siam Tour Partners',
                'vendor_code' => 'VDR-SIAM02',
                'contact_person' => 'Nok Siriwan',
                'email' => 'dispatch@siamtour.example',
                'phone' => '+66-89-222-9002',
                'line_id' => 'siamtour',
                'service_type' => 'tour',
                'default_commission_rate' => 10,
                'is_active' => true,
                'notes' => 'Day tour and guide operations',
            ],
            [
                'name' => 'Thai Mobility Group',
                'vendor_code' => 'VDR-THAI03',
                'contact_person' => 'Anan Chaiyo',
                'email' => 'team@thaimobility.example',
                'phone' => '+66-92-333-4503',
                'line_id' => 'thaimobility',
                'service_type' => 'both',
                'default_commission_rate' => 11,
                'is_active' => true,
                'notes' => 'Mixed fleet for car and tour operations',
            ],
        ])->map(fn (array $vendorData) => Vendor::query()->updateOrCreate(
            ['vendor_code' => $vendorData['vendor_code']],
            $vendorData
        ));

        $bookings = Booking::query()
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->latest('id')
            ->limit(6)
            ->get();

        if ($bookings->isEmpty()) {
            $bookings = $this->seedDemoBookings();
        }

        if ($bookings->isEmpty()) {
            return;
        }

        $adminId = User::query()->where('email', 'admin@example.com')->value('id')
            ?? User::query()->orderBy('id')->value('id');

        $driverPool = [
            ['name' => 'Driver Aek', 'phone' => '+66-81-000-1001', 'plate' => '1กข-1024'],
            ['name' => 'Driver Mint', 'phone' => '+66-81-000-1002', 'plate' => '2กง-4409'],
            ['name' => 'Driver Korn', 'phone' => '+66-81-000-1003', 'plate' => '3ขฉ-1188'],
            ['name' => 'Driver Ploy', 'phone' => '+66-81-000-1004', 'plate' => '4คม-5521'],
        ];

        foreach ($bookings as $index => $booking) {
            $vendor = $vendors[$index % $vendors->count()];
            $driver = $driverPool[$index % count($driverPool)];
            $commissionRate = (float) ($vendor->default_commission_rate ?? 10);
            $commissionAmount = round(((float) $booking->total_price * $commissionRate) / 100, 2);
            $payout = round(max((float) $booking->total_price - $commissionAmount, 0), 2);
            $dispatchStatus = match ($index % 3) {
                0 => 'assigned',
                1 => 'on_route',
                default => 'completed',
            };
            $settlementStatus = $dispatchStatus === 'completed' ? 'paid' : 'unpaid';

            DispatchAssignment::query()->updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'vendor_id' => $vendor->id,
                    'assigned_by' => $adminId,
                    'driver_name' => $driver['name'],
                    'driver_phone' => $driver['phone'],
                    'vehicle_plate' => $driver['plate'],
                    'dispatch_status' => $dispatchStatus,
                    'assigned_at' => now()->subDays(max(0, 3 - $index)),
                    'dispatch_notes' => 'Seeded assignment for MVP operations flow.',
                    'commission_type' => 'percentage',
                    'commission_rate' => $commissionRate,
                    'commission_flat_amount' => null,
                    'commission_amount' => $commissionAmount,
                    'vendor_payout_amount' => $payout,
                    'settlement_status' => $settlementStatus,
                    'settled_at' => $settlementStatus === 'paid' ? now()->subDay() : null,
                    'settlement_notes' => $settlementStatus === 'paid'
                        ? 'Settled in weekly payout batch.'
                        : 'Awaiting settlement cycle.',
                ]
            );
        }
    }

    private function seedDemoBookings()
    {
        $userId = User::query()->where('email', 'user@example.com')->value('id')
            ?? User::query()->orderBy('id')->value('id');

        $products = Product::query()->where('is_active', true)->orderBy('id')->limit(4)->get();

        if (!$userId || $products->isEmpty()) {
            return collect();
        }

        $bookings = collect();

        foreach ($products as $index => $product) {
            $baseTotal = (float) ($product->final_price ?? $product->base_price ?? 1000);
            $bookingCode = 'TRV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

            $bookings->push(Booking::query()->create([
                'booking_code' => $bookingCode,
                'user_id' => $userId,
                'guest_name' => 'Demo Customer ' . ($index + 1),
                'guest_email' => 'demo.customer' . ($index + 1) . '@example.com',
                'guest_phone' => '+66-80-100-20' . str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'service_type' => $product->type === 'tour' ? 'tour' : 'car',
                'service_subtype' => $product->type === 'tour' ? 'day-tour' : 'airport-transfer',
                'destination' => $product->destination ?? 'Bangkok',
                'experience_type' => $product->category ?? null,
                'meeting_point_confirmed' => $product->type === 'tour',
                'selected_add_ons' => [],
                'product_id' => $product->id,
                'product_name' => $product->name,
                'service_date' => now()->addDays($index + 1)->toDateString(),
                'service_time' => '09:00:00',
                'pickup_location' => 'Hotel Pickup Point',
                'dropoff_location' => 'Service Destination',
                'flight_number' => $product->type === 'car' ? 'TG10' . ($index + 2) : null,
                'quantity' => 1,
                'adult_pax' => $product->type === 'tour' ? 2 : null,
                'child_pax' => $product->type === 'tour' ? 0 : null,
                'total_price' => $baseTotal,
                'add_ons_total' => 0,
                'currency' => 'THB',
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'paid_at' => now()->subDay(),
                'special_request' => 'Demo seeded booking for dispatch testing.',
                'admin_notes' => 'Auto generated by VendorOperationsSeeder',
            ]));
        }

        return $bookings;
    }
}
