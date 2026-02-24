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
            if (!Schema::hasColumn('bookings', 'selected_add_ons')) {
                $table->json('selected_add_ons')->nullable()->after('meeting_point_confirmed');
            }

            if (!Schema::hasColumn('bookings', 'add_ons_total')) {
                $table->decimal('add_ons_total', 15, 2)->default(0)->after('total_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'selected_add_ons')) {
                $table->dropColumn('selected_add_ons');
            }

            if (Schema::hasColumn('bookings', 'add_ons_total')) {
                $table->dropColumn('add_ons_total');
            }
        });
    }
};
