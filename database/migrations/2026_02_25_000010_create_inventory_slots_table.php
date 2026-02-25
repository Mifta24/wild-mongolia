<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->date('slot_date')->index();
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->unsignedInteger('capacity')->default(1);
            $table->unsignedInteger('booked_quantity')->default(0);
            $table->unsignedInteger('cutoff_minutes')->default(120);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'slot_date', 'start_time'], 'inventory_slots_unique_product_datetime');
            $table->index(['product_id', 'slot_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_slots');
    }
};
