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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['fixed', 'percentage']); // THB atau %
            $table->decimal('value', 10, 2); // Nilai diskon
            $table->decimal('min_purchase', 10, 2)->nullable(); // Minimum pembelian
            $table->decimal('max_discount', 10, 2)->nullable(); // Max discount untuk percentage
            $table->enum('applicable_to', ['all', 'car', 'tour'])->default('all');
            $table->integer('usage_limit')->nullable(); // Total bisa dipakai berapa kali
            $table->integer('usage_per_user')->default(1); // Per user limit
            $table->integer('usage_count')->default(0); // Counter penggunaan
            $table->date('valid_from');
            $table->date('valid_until');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
