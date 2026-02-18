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
        // Add expired_at column to point_ledgers if not exists
        if (!Schema::hasColumn('point_ledgers', 'expired_at')) {
            Schema::table('point_ledgers', function (Blueprint $table) {
                $table->timestamp('expired_at')->nullable()->after('expires_at');
            });
        }

        // Add lifetime_points column to users table for tracking membership tier
        if (!Schema::hasColumn('users', 'lifetime_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('lifetime_points')->default(0)->after('points');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_ledgers', function (Blueprint $table) {
            $table->dropColumn('expired_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('lifetime_points');
        });
    }
};
