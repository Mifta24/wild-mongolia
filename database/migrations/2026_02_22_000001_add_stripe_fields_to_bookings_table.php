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
            $table->string('stripe_checkout_session_id')->nullable()->unique()->after('payment_status');
            $table->string('stripe_payment_intent_id')->nullable()->index()->after('stripe_checkout_session_id');
            $table->string('stripe_receipt_url')->nullable()->after('stripe_payment_intent_id');
            $table->timestamp('paid_at')->nullable()->after('stripe_receipt_url');
            $table->timestamp('refunded_at')->nullable()->after('paid_at');
            $table->decimal('refund_amount', 15, 2)->nullable()->after('refunded_at');
            $table->string('invoice_number')->nullable()->unique()->after('refund_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['stripe_checkout_session_id']);
            $table->dropIndex(['stripe_payment_intent_id']);
            $table->dropUnique(['invoice_number']);
            $table->dropColumn([
                'stripe_checkout_session_id',
                'stripe_payment_intent_id',
                'stripe_receipt_url',
                'paid_at',
                'refunded_at',
                'refund_amount',
                'invoice_number',
            ]);
        });
    }
};
