<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // CARS
        $cars = [
            [
                'name' => 'Standard Sedan',
                'slug' => 'standard-sedan',
                'type' => 'car',
                'description' => 'Comfortable sedan perfect for airport transfers and city rides. Spacious interior with air conditioning.',
                'image_url' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?q=80&w=1470&auto=format&fit=crop',
                'car_model' => 'Toyota Camry or similar',
                'vehicle_type' => 'sedan',
                'transmission' => 'automatic',
                'is_air_conditioned' => true,
                'year_manufactured' => 2023,
                'max_passengers' => 3,
                'max_luggage' => 2,
                'base_price' => 1000,
                'distance_price_per_km' => 22,
                'minimum_distance_price' => 900,
                'is_active' => true,
                'is_featured' => true,
                'total_reviews' => 450,
                'average_rating' => 4.8,
            ],
            [
                'name' => 'Luxury Sedan',
                'slug' => 'luxury-sedan',
                'type' => 'car',
                'description' => 'Premium sedan with leather seats and executive service. Perfect for business travelers.',
                'image_url' => 'https://images.unsplash.com/photo-1563720360172-67b8f3dce741?q=80&w=1470&auto=format&fit=crop',
                'car_model' => 'Mercedes E-Class or similar',
                'vehicle_type' => 'sedan',
                'transmission' => 'automatic',
                'is_air_conditioned' => true,
                'year_manufactured' => 2024,
                'max_passengers' => 3,
                'max_luggage' => 2,
                'base_price' => 1800,
                'distance_price_per_km' => 35,
                'minimum_distance_price' => 1500,
                'is_active' => true,
                'is_featured' => false,
                'total_reviews' => 320,
                'average_rating' => 4.9,
            ],
            [
                'name' => 'SUV / Van',
                'slug' => 'suv-van',
                'type' => 'car',
                'description' => 'Spacious SUV or van for groups and families. Extra luggage space included.',
                'image_url' => 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?q=80&w=1471&auto=format&fit=crop',
                'car_model' => 'Toyota Alphard or similar',
                'vehicle_type' => 'van',
                'transmission' => 'automatic',
                'is_air_conditioned' => true,
                'year_manufactured' => 2022,
                'max_passengers' => 6,
                'max_luggage' => 4,
                'base_price' => 2500,
                'distance_price_per_km' => 42,
                'minimum_distance_price' => 2200,
                'is_active' => true,
                'is_featured' => false,
                'total_reviews' => 280,
                'average_rating' => 4.7,
            ],
        ];

        // TOURS
        $tours = [
            [
                'name' => 'Gobi Desert Dunes & Camel Trek',
                'slug' => 'gobi-desert-camel-trek',
                'type' => 'tour',
                'description' => 'Ride camels across the Khongoryn Els singing dunes and camp under the desert stars.',
                'image_url' => url('images/gobi.jpg'),
                'destination' => 'Gobi Desert',
                'duration' => '2 Days',
                'category' => 'nature',
                'language' => 'english',
                'includes_lunch' => true,
                'includes_pickup' => true,
                'base_price' => 1500,
                'discounted_price' => 1200,
                'is_active' => true,
                'is_featured' => true,
                'total_reviews' => 520,
                'average_rating' => 4.9,
            ],
            [
                'name' => 'Eagle Hunter Family Homestay',
                'slug' => 'eagle-hunter-homestay',
                'type' => 'tour',
                'description' => 'Stay with a Kazakh eagle hunter family, meet their golden eagles and learn traditional falconry.',
                'image_url' => url('images/eagle.jpg'),
                'destination' => 'Bayan-Ölgii',
                'duration' => '1 Day',
                'category' => 'culture',
                'language' => 'english',
                'includes_lunch' => true,
                'includes_pickup' => true,
                'base_price' => 2500,
                'is_active' => true,
                'is_featured' => true,
                'total_reviews' => 285,
                'average_rating' => 4.7,
            ],
            [
                'name' => 'Terelj National Park Ger Stay',
                'slug' => 'terelj-ger-stay',
                'type' => 'tour',
                'description' => 'Overnight in a traditional ger with horse riding and hiking to Turtle Rock.',
                'image_url' => url('images/hero.jpg'),
                'destination' => 'Terelj',
                'duration' => '2 Days',
                'category' => 'nature',
                'language' => 'english',
                'includes_lunch' => true,
                'includes_pickup' => true,
                'base_price' => 1800,
                'is_active' => true,
                'is_featured' => false,
                'total_reviews' => 410,
                'average_rating' => 4.8,
            ],
            [
                'name' => 'Kharkhorin & Erdene Zuu Monastery',
                'slug' => 'kharkhorin-erdene-zuu',
                'type' => 'tour',
                'description' => 'Full day trip to the ancient Mongol capital and one of the oldest Buddhist monasteries in the country.',
                'image_url' => url('images/hero.jpg'),
                'destination' => 'Kharkhorin',
                'duration' => '8 Hours',
                'category' => 'culture',
                'language' => 'english',
                'includes_lunch' => true,
                'includes_pickup' => true,
                'base_price' => 2200,
                'is_active' => true,
                'is_featured' => false,
                'total_reviews' => 390,
                'average_rating' => 4.8,
            ],
            [
                'name' => 'Ulaanbaatar Food & Culture Night',
                'slug' => 'ulaanbaatar-food-night',
                'type' => 'tour',
                'description' => 'Sample buuz, khuushuur and airag with a local guide, then visit Gandan Monastery.',
                'image_url' => url('images/hero.jpg'),
                'destination' => 'Ulaanbaatar',
                'duration' => '3 Hours',
                'category' => 'food',
                'language' => 'english',
                'includes_lunch' => false,
                'includes_pickup' => true,
                'base_price' => 990,
                'is_active' => true,
                'is_featured' => true,
                'total_reviews' => 680,
                'average_rating' => 4.9,
            ],
            [
                'name' => 'Chinggis Khaan Statue & Nomad Show',
                'slug' => 'chinggis-khaan-nomad-show',
                'type' => 'tour',
                'description' => 'Visit the giant Chinggis Khaan statue and enjoy a traditional throat singing and folk dance show.',
                'image_url' => url('images/hero.jpg'),
                'destination' => 'Ulaanbaatar',
                'duration' => '5 Hours',
                'category' => 'culture',
                'language' => 'english',
                'includes_lunch' => false,
                'includes_pickup' => true,
                'base_price' => 1400,
                'is_active' => true,
                'is_featured' => false,
                'total_reviews' => 350,
                'average_rating' => 4.6,
            ],
        ];

        $cars = array_map(function (array $car) {
            return array_merge([
                'gallery_images' => [
                    $car['image_url'] ?? null,
                    'https://images.unsplash.com/photo-1485291571150-772bcfc10da5?q=80&w=1470&auto=format&fit=crop',
                ],
                'itinerary' => "Pickup at requested point\nMeet & greet with driver\nTransfer to destination\nDrop-off support",
                'add_ons' => [
                    ['name' => 'Child Seat', 'price' => 200, 'description' => 'Per seat, subject to availability'],
                    ['name' => 'Extended Waiting (30 min)', 'price' => 300, 'description' => 'For airport pickup delay'],
                    ['name' => 'Extra Stop', 'price' => 250, 'description' => 'One stop on route'],
                ],
                'cancellation_policy' => "Free cancellation up to 24 hours before pickup.\n50% charge for cancellation within 24 hours.\nNo-show: 100% charge.",
                'meeting_point_name' => 'Main Arrival Hall Exit',
                'meeting_point_address' => 'Suvarnabhumi Airport, Level 2, Exit Gate 3',
                'meeting_point_lat' => 13.6900000,
                'meeting_point_lng' => 100.7501120,
            ], $car);
        }, $cars);

        $tours = array_map(function (array $tour) {
            return array_merge([
                'gallery_images' => [
                    $tour['image_url'] ?? null,
                    'https://images.unsplash.com/photo-1526481280695-3c46980e1b66?q=80&w=1470&auto=format&fit=crop',
                ],
                'itinerary' => "Meet at designated point\nGuided activity start\nMain highlights visit\nFree time & wrap-up",
                'add_ons' => [
                    ['name' => 'Hotel Pickup Upgrade', 'price' => 350, 'description' => 'Round-trip transfer from central area'],
                    ['name' => 'Professional Photo Set', 'price' => 500, 'description' => '10 edited photos'],
                ],
                'cancellation_policy' => "Free cancellation up to 48 hours before start time.\n50% charge for cancellation within 48 hours.\nNo-show: 100% charge.",
                'meeting_point_name' => 'Tour Meeting Point',
                'meeting_point_address' => 'Please arrive 15 minutes before start time.',
                'meeting_point_lat' => 13.7563000,
                'meeting_point_lng' => 100.5018000,
            ], $tour);
        }, $tours);

        foreach ($cars as $car) {
            Product::updateOrCreate(
                ['slug' => $car['slug']],
                $car
            );
        }

        foreach ($tours as $tour) {
            Product::updateOrCreate(
                ['slug' => $tour['slug']],
                $tour
            );
        }
    }
}
