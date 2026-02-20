<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Distance-based pricing for cars
            $table->decimal('distance_price_per_km', 8, 2)->nullable()->after('discounted_price');
            $table->decimal('minimum_distance_price', 10, 2)->nullable()->after('distance_price_per_km');

            // Vehicle category field
            $table->string('vehicle_type')->nullable()->after('car_model')->comment('sedan, suv, van, minibus');

            // Tour-specific filters
            $table->string('category')->nullable()->after('destination')->comment('temples, food, sea, elephants, culture');
            $table->string('language')->default('english')->after('category');

            // Additional car details
            $table->string('transmission')->nullable()->after('vehicle_type')->comment('automatic, manual');
            $table->boolean('is_air_conditioned')->default(true)->after('transmission');
            $table->integer('year_manufactured')->nullable()->after('is_air_conditioned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('distance_price_per_km');
            $table->dropColumn('minimum_distance_price');
            $table->dropColumn('vehicle_type');
            $table->dropColumn('category');
            $table->dropColumn('language');
            $table->dropColumn('transmission');
            $table->dropColumn('is_air_conditioned');
            $table->dropColumn('year_manufactured');
        });
    }
};
