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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'membership_started_at')) {
                $table->timestamp('membership_started_at')->nullable()->after('membership_tier');
            }

            if (!Schema::hasColumn('users', 'membership_expires_at')) {
                $table->timestamp('membership_expires_at')->nullable()->after('membership_started_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('users', 'membership_started_at')) {
                $columnsToDrop[] = 'membership_started_at';
            }

            if (Schema::hasColumn('users', 'membership_expires_at')) {
                $columnsToDrop[] = 'membership_expires_at';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
