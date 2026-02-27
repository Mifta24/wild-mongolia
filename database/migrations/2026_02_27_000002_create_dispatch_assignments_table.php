<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->restrictOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('driver_name');
            $table->string('driver_phone')->nullable();
            $table->string('vehicle_plate')->nullable();

            $table->enum('dispatch_status', ['pending', 'assigned', 'on_route', 'completed', 'cancelled'])->default('assigned');
            $table->timestamp('assigned_at')->nullable();
            $table->text('dispatch_notes')->nullable();

            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->decimal('commission_flat_amount', 12, 2)->nullable();
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('vendor_payout_amount', 12, 2)->default(0);

            $table->enum('settlement_status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid');
            $table->timestamp('settled_at')->nullable();
            $table->text('settlement_notes')->nullable();

            $table->timestamps();

            $table->unique('booking_id');
            $table->index(['vendor_id', 'dispatch_status']);
            $table->index(['vendor_id', 'settlement_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_assignments');
    }
};
