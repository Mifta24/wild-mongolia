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
            if (!Schema::hasColumn('bookings', 'service_subtype')) {
                $table->string('service_subtype')->nullable()->after('service_type');
            }

            if (!Schema::hasColumn('bookings', 'destination')) {
                $table->string('destination')->nullable()->after('service_subtype');
            }

            if (!Schema::hasColumn('bookings', 'experience_type')) {
                $table->string('experience_type')->nullable()->after('destination');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['service_subtype', 'destination', 'experience_type']);
        });
    }
};
