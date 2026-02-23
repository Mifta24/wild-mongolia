<?php

namespace App\Services;

use App\Models\Booking;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Refund;
use Stripe\StripeClient;

class StripePaymentService
{
    private StripeClient $client;

    public function __construct()
    {
        $secret = (string) config('services.stripe.secret');

        if (blank($secret)) {
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        $this->client = new StripeClient($secret);
    }

    /**
     * @throws ApiErrorException
     */
    public function createCheckoutSession(Booking $booking): Session
    {
        return $this->client->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => $booking->guest_email,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower($booking->currency ?? 'THB'),
                    'unit_amount' => $this->toStripeAmount((float) $booking->total_price),
                    'product_data' => [
                        'name' => $booking->product_name,
                        'description' => 'Booking #' . $booking->booking_code,
                    ],
                ],
            ]],
            'metadata' => [
                'booking_id' => (string) $booking->id,
                'booking_code' => $booking->booking_code,
            ],
            'success_url' => route('booking.success', $booking->id) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('booking.payment', $booking->id) . '?cancelled=1',
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    public function retrieveCheckoutSession(string $sessionId): Session
    {
        return $this->client->checkout->sessions->retrieve($sessionId, []);
    }

    /**
     * @throws ApiErrorException
     */
    public function refundBooking(Booking $booking, ?int $amountInMinor = null): Refund
    {
        if (blank($booking->stripe_payment_intent_id)) {
            throw new \RuntimeException('No Stripe payment intent found for this booking.');
        }

        $payload = [
            'payment_intent' => $booking->stripe_payment_intent_id,
            'metadata' => [
                'booking_id' => (string) $booking->id,
                'booking_code' => $booking->booking_code,
            ],
        ];

        if (!is_null($amountInMinor)) {
            $payload['amount'] = $amountInMinor;
        }

        return $this->client->refunds->create($payload);
    }

    public function toStripeAmount(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
