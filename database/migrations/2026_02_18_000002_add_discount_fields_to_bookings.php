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
        Schema::table('bookings', function (Blueprint $table) {
            // Add columns to store discount information
            if (!Schema::hasColumn('bookings', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('total_price');
            }

            if (!Schema::hasColumn('bookings', 'discount_info')) {
                $table->json('discount_info')->nullable()->after('original_price');
            }

            if (!Schema::hasColumn('bookings', 'points_used')) {
                $table->integer('points_used')->default(0)->after('discount_info');
            }

            if (!Schema::hasColumn('bookings', 'coupon_id')) {
                $table->foreignId('coupon_id')->nullable()->after('points_used')->constrained()->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'discount_info', 'points_used']);
            $table->dropForeign(['coupon_id']);
            $table->dropColumn('coupon_id');
        });
    }
};
