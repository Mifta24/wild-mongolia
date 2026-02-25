<?php

namespace Database\Seeders;

use App\Models\InventorySlot;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InventorySlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::query()
            ->where('is_active', true)
            ->get(['id', 'type']);

        $baseDates = collect(range(0, 6))
            ->map(fn (int $offset) => Carbon::today()->addDays($offset));

        foreach ($products as $product) {
            $timeBands = $product->type === 'tour'
                ? [
                    ['08:00', '12:00'],
                    ['13:00', '17:00'],
                    ['18:00', '21:00'],
                ]
                : [
                    ['07:00', '09:00'],
                    ['11:00', '13:00'],
                    ['15:00', '17:00'],
                ];

            $baseCapacity = $product->type === 'tour' ? 12 : 6;
            $cutoffMinutes = $product->type === 'tour' ? 180 : 120;

            foreach ($baseDates as $date) {
                foreach ($timeBands as [$startTime, $endTime]) {
                    $capacity = $baseCapacity + random_int(0, 4);

                    InventorySlot::query()->updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'slot_date' => $date->toDateString(),
                            'start_time' => $startTime,
                        ],
                        [
                            'end_time' => $endTime,
                            'capacity' => $capacity,
                            'booked_quantity' => random_int(0, max(0, $capacity - 1)),
                            'cutoff_minutes' => $cutoffMinutes,
                            'is_active' => true,
                            'notes' => 'Seeded sample slot',
                        ]
                    );
                }
            }
        }
    }
}
