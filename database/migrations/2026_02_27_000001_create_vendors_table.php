<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vendor_code')->unique();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('line_id')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('service_type')->nullable();
            $table->text('address')->nullable();
            $table->decimal('default_commission_rate', 5, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'service_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
