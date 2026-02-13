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
        Schema::create('point_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['earned', 'used', 'expired', 'refunded']);
            $table->integer('points'); // Positive untuk earned/refunded, negative untuk used/expired
            $table->integer('balance_after'); // Running balance setelah transaksi
            $table->string('source')->nullable(); // 'booking', 'referral', 'admin', etc
            $table->unsignedBigInteger('booking_id')->nullable(); // Relasi jika dari booking
            $table->string('description')->nullable();
            $table->date('expires_at')->nullable(); // Untuk earned points (12 bulan)
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_ledgers');
    }
};
