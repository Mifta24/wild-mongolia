<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload = $request->getContent();
        $signature = (string) $request->header('Stripe-Signature');
        $secret = (string) config('services.stripe.webhook_secret');

        if (blank($secret)) {
            Log::warning('Stripe webhook secret is not configured.');

            return response()->json(['message' => 'Webhook secret not configured.'], 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (SignatureVerificationException $exception) {
            Log::warning('Invalid Stripe webhook signature.', ['error' => $exception->getMessage()]);

            return response()->json(['message' => 'Invalid signature'], 400);
        } catch (\UnexpectedValueException $exception) {
            Log::warning('Invalid Stripe webhook payload.', ['error' => $exception->getMessage()]);

            return response()->json(['message' => 'Invalid payload'], 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event->data->object),
            'charge.refunded' => $this->handleChargeRefunded($event->data->object),
            default => null,
        };

        return response()->json(['received' => true]);
    }

    private function handleCheckoutSessionCompleted(object $session): void
    {
        $bookingId = data_get($session, 'metadata.booking_id');

        $booking = $bookingId ? Booking::find($bookingId) : null;

        if (!$booking) {
            $booking = Booking::where('stripe_checkout_session_id', $session->id)->first();
        }

        if (!$booking) {
            Log::warning('Stripe checkout session completed but booking not found.', ['session_id' => $session->id]);

            return;
        }

        $booking->update([
            'stripe_checkout_session_id' => $session->id,
            'stripe_payment_intent_id' => $session->payment_intent,
            'payment_status' => 'paid',
            'status' => $booking->status === 'pending' ? 'confirmed' : $booking->status,
            'paid_at' => $booking->paid_at ?? now(),
            'stripe_receipt_url' => data_get($session, 'receipt_url'),
        ]);
    }

    private function handleChargeRefunded(object $charge): void
    {
        if (blank($charge->payment_intent)) {
            return;
        }

        $booking = Booking::where('stripe_payment_intent_id', $charge->payment_intent)->first();

        if (!$booking) {
            return;
        }

        $booking->update([
            'payment_status' => 'refunded',
            'status' => $booking->status === 'cancelled' ? 'cancelled' : $booking->status,
            'refunded_at' => now(),
            'refund_amount' => ((int) ($charge->amount_refunded ?? 0)) / 100,
        ]);
    }
}
