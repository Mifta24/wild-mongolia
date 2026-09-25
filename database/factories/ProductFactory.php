<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['car', 'tour']);
        $name = $type === 'car'
            ? $this->faker->randomElement(['Standard Sedan', 'Luxury Sedan', 'Family SUV', 'Minivan'])
            : $this->faker->randomElement(['City Highlights Tour', 'Temple Discovery Tour', 'Food Experience Tour', 'Island Day Trip']);

        $basePrice = $type === 'car'
            ? $this->faker->numberBetween(900, 3000)
            : $this->faker->numberBetween(800, 2800);

        $hasDiscount = $this->faker->boolean(35);

        return [
            'name' => $name,
            'slug' => Str::slug($name . '-' . $this->faker->unique()->numberBetween(1000, 9999)),
            'type' => $type,
            'description' => $this->faker->sentence(16),
            'image_url' => null,

            'car_model' => $type === 'car' ? $this->faker->randomElement(['Toyota Camry', 'Honda Accord', 'Toyota Fortuner', 'Hyundai Staria']) : null,
            'vehicle_type' => $type === 'car' ? $this->faker->randomElement(['sedan', 'suv', 'van']) : null,
            'transmission' => $type === 'car' ? $this->faker->randomElement(['automatic', 'manual']) : null,
            'is_air_conditioned' => $type === 'car' ? true : false,
            'year_manufactured' => $type === 'car' ? $this->faker->numberBetween(2018, 2025) : null,
            'max_passengers' => $type === 'car' ? $this->faker->numberBetween(3, 10) : null,
            'max_luggage' => $type === 'car' ? $this->faker->numberBetween(2, 8) : null,

            'destination' => $type === 'tour' ? $this->faker->randomElement(['Ulaanbaatar', 'Gobi Desert', 'Terelj', 'Kharkhorin']) : null,
            'duration' => $type === 'tour' ? $this->faker->randomElement(['2 Hours', '4 Hours', '8 Hours', 'Full Day']) : null,
            'category' => $type === 'tour' ? $this->faker->randomElement(['culture', 'food', 'sea', 'nature']) : null,
            'language' => $type === 'tour' ? $this->faker->randomElement(['english', 'mongolian', 'chinese']) : 'english',
            'includes_lunch' => $type === 'tour' ? $this->faker->boolean(45) : false,
            'includes_pickup' => $type === 'tour' ? $this->faker->boolean(70) : false,

            'base_price' => $basePrice,
            'discounted_price' => $hasDiscount ? max(100, $basePrice - $this->faker->numberBetween(100, 400)) : null,
            'distance_price_per_km' => $type === 'car' ? $this->faker->numberBetween(18, 45) : null,
            'minimum_distance_price' => $type === 'car' ? $this->faker->numberBetween(600, 1300) : null,
            'currency' => 'THB',
            'is_active' => true,
            'is_featured' => $this->faker->boolean(30),
            'total_reviews' => $this->faker->numberBetween(0, 800),
            'average_rating' => $this->faker->randomFloat(2, 3.8, 5),
        ];
    }
}
