<?php

namespace App\Http\Controllers;

use App\Enums\MembershipTier;
use App\Models\Booking;
use App\Models\User;
use App\Services\CouponService;
use App\Services\PointService;
use Illuminate\Support\Facades\DB;
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
        if (data_get($session, 'metadata.purpose') === 'membership_subscription') {
            $this->handleMembershipCheckoutSessionCompleted($session);

            return;
        }

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

    private function handleMembershipCheckoutSessionCompleted(object $session): void
    {
        $userId = (int) data_get($session, 'metadata.user_id');
        $user = $userId > 0 ? User::find($userId) : null;

        if (!$user) {
            Log::warning('Stripe membership checkout completed but user not found.', [
                'session_id' => (string) ($session->id ?? ''),
                'user_id' => $userId,
            ]);

            return;
        }

        $sessionId = (string) ($session->id ?? '');
        $alreadyProcessed = DB::table('membership_payments')
            ->where('stripe_checkout_session_id', $sessionId)
            ->exists();

        if ($alreadyProcessed) {
            return;
        }

        try {
            $tier = MembershipTier::from((string) data_get($session, 'metadata.tier', MembershipTier::SILVER->value));
        } catch (\ValueError $exception) {
            Log::warning('Stripe membership checkout has invalid tier metadata.', [
                'session_id' => $sessionId,
                'tier' => (string) data_get($session, 'metadata.tier', ''),
            ]);

            return;
        }

        if (!$tier->isPaidPlan()) {
            Log::warning('Stripe membership checkout has non-paid tier metadata.', [
                'session_id' => $sessionId,
                'tier' => $tier->value,
            ]);

            return;
        }

        $action = (string) data_get($session, 'metadata.action', 'subscribe');
        $startsAt = now(config('app.timezone'));

        if (
            $action === 'renew'
            && $user->membership_tier === $tier->value
            && $user->membership_expires_at
            && $user->membership_expires_at->isFuture()
        ) {
            $startsAt = $user->membership_expires_at->copy()->addSecond();
        }

        DB::transaction(function () use ($user, $tier, $action, $session, $startsAt): void {
            $user->activateMembership($tier, $startsAt);

            DB::table('membership_payments')->insert([
                'user_id' => $user->id,
                'tier' => $tier->value,
                'action' => $action,
                'stripe_checkout_session_id' => (string) ($session->id ?? ''),
                'stripe_payment_intent_id' => (string) ($session->payment_intent ?? ''),
                'amount_thb' => (int) (((int) ($session->amount_total ?? 0)) / 100),
                'processed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $couponService = app(CouponService::class);
        if ($tier === MembershipTier::GOLD) {
            $couponService->issueGoldMembershipCoupon($user->fresh(), $action);
        }

        if ($tier === MembershipTier::PLATINUM) {
            $couponService->issuePlatinumMembershipCoupon($user->fresh(), $action);
        }

        app(PointService::class)->awardMembershipCashback(
            $user->fresh(),
            $tier,
            $action,
            $sessionId
        );
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
