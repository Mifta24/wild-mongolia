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
                'name' => 'Grand Palace & Emerald Buddha',
                'slug' => 'grand-palace-tour',
                'type' => 'tour',
                'description' => 'Explore the magnificent Grand Palace and Temple of the Emerald Buddha. A must-see for first-time visitors to Bangkok.',
                'image_url' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?q=80&w=1949&auto=format&fit=crop',
                'destination' => 'Bangkok',
                'duration' => '4 Hours',
                'category' => 'culture',
                'language' => 'english',
                'includes_lunch' => false,
                'includes_pickup' => true,
                'base_price' => 1500,
                'discounted_price' => 1200,
                'is_active' => true,
                'is_featured' => true,
                'total_reviews' => 520,
                'average_rating' => 4.9,
            ],
            [
                'name' => 'Ethical Elephant Sanctuary',
                'slug' => 'elephant-sanctuary',
                'type' => 'tour',
                'description' => 'Spend a day with rescued elephants in their natural habitat. Feed, bathe, and learn about these gentle giants.',
                'image_url' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=1938&auto=format&fit=crop',
                'destination' => 'Chiang Mai',
                'duration' => '6 Hours',
                'category' => 'nature',
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
                'name' => 'Floating Market Experience',
                'slug' => 'floating-market',
                'type' => 'tour',
                'description' => 'Traditional floating market tour with boat ride. Experience authentic Thai culture and local cuisine.',
                'image_url' => 'https://images.unsplash.com/photo-1528181304800-259b08848526?q=80&w=1470&auto=format&fit=crop',
                'destination' => 'Bangkok',
                'duration' => '5 Hours',
                'category' => 'culture',
                'language' => 'english',
                'includes_lunch' => false,
                'includes_pickup' => true,
                'base_price' => 1800,
                'is_active' => true,
                'is_featured' => false,
                'total_reviews' => 410,
                'average_rating' => 4.8,
            ],
            [
                'name' => 'Ayutthaya Ancient City',
                'slug' => 'ayutthaya-tour',
                'type' => 'tour',
                'description' => 'Full day UNESCO World Heritage site tour. Explore ancient temples and ruins of the former Siamese capital.',
                'image_url' => 'https://images.unsplash.com/photo-1563492065049-4b2a36b9a75c?q=80&w=1374&auto=format&fit=crop',
                'destination' => 'Ayutthaya',
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
                'name' => 'Street Food Night Tour',
                'slug' => 'food-night-tour',
                'type' => 'tour',
                'description' => 'Culinary adventure through Bangkok\'s best street food spots. Try authentic Thai dishes with a local guide.',
                'image_url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=1470&auto=format&fit=crop',
                'destination' => 'Bangkok',
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
                'name' => 'Chao Phraya Dinner Cruise',
                'slug' => 'dinner-cruise',
                'type' => 'tour',
                'description' => 'Romantic dinner cruise along the Chao Phraya River. Buffet dinner with live music and stunning views.',
                'image_url' => 'https://images.unsplash.com/photo-1578649833567-d95c76b07a06?q=80&w=1471&auto=format&fit=crop',
                'destination' => 'Bangkok',
                'duration' => '2.5 Hours',
                'category' => 'sea',
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
