<?php

namespace App\Services;

use App\Enums\MembershipTier;
use App\Models\Booking;
use App\Models\User;
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
                    'currency' => strtolower($booking->currency ?? 'MNT'),
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
    public function createMembershipCheckoutSession(User $user, MembershipTier $tier, string $action = 'subscribe'): Session
    {
        $priceThb = $tier->getYearlyPriceThb();

        if (is_null($priceThb) || $priceThb <= 0) {
            throw new \RuntimeException('Selected membership tier is not available for direct online payment.');
        }

        return $this->client->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => $user->email,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'mnt',
                    'unit_amount' => $this->toStripeAmount((float) $priceThb),
                    'product_data' => [
                        'name' => $tier->label() . ' Subscription (1 Year)',
                        'description' => 'Membership ' . $action . ' for user #' . $user->id,
                    ],
                ],
            ]],
            'metadata' => [
                'purpose' => 'membership_subscription',
                'user_id' => (string) $user->id,
                'tier' => $tier->value,
                'action' => $action,
            ],
            'success_url' => route('user.membership.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('membership') . '?membership_cancelled=1',
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
