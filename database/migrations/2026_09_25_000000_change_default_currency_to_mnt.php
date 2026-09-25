<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['products', 'bookings'] as $table) {
            Schema::table($table, function ($t) {
                $t->string('currency', 3)->default('MNT')->change();
            });

            DB::table($table)->where('currency', 'THB')->update(['currency' => 'MNT']);
        }
    }

    public function down(): void
    {
        foreach (['products', 'bookings'] as $table) {
            Schema::table($table, function ($t) {
                $t->string('currency', 3)->default('THB')->change();
            });
        }
    }
};
