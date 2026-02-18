<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\PointLedger;
use App\Enums\MembershipTier;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PointService
{
    /**
     * Calculate points earned from a booking
     */
    public function calculateBookingPoints(Booking $booking): int
    {
        // Base: 1 point per 100 THB spent
        $basePoints = (int) floor($booking->total_price / 100);

        if ($booking->user) {
            $tier = MembershipTier::from($booking->user->membership_tier ?? 'silver');
            $basePoints = (int) floor($basePoints * $tier->getPointMultiplier());
        }

        return max(1, $basePoints); // Minimum 1 point
    }

    /**
     * Award points for a booking
     */
    public function awardBookingPoints(Booking $booking): ?PointLedger
    {
        if (!$booking->user) {
            return null; // Guest bookings don't earn points
        }

        if ($booking->payment_status !== 'paid') {
            return null; // Only paid bookings earn points
        }

        $points = $this->calculateBookingPoints($booking);

        return $booking->user->addPoints(
            $points,
            'booking',
            $booking->id,
            "Points from booking #{$booking->booking_code}"
        );
    }

    /**
     * Redeem points for discount
     */
    public function redeemPoints(User $user, int $points): float
    {
        if ($user->points < $points) {
            throw new \Exception('Insufficient points');
        }

        // 100 points = 100 THB discount
        $discount = $points;

        // Apply tier bonus
        $tier = MembershipTier::from($user->membership_tier ?? 'silver');
        $bonusMultiplier = match($tier) {
            MembershipTier::GOLD => 1.10,
            MembershipTier::PLATINUM => 1.20,
            default => 1.0,
        };

        return $discount * $bonusMultiplier;
    }

    /**
     * Use points for a booking
     */
    public function usePointsForBooking(User $user, int $points, Booking $booking): PointLedger
    {
        return $user->deductPoints(
            $points,
            'booking_discount',
            $booking->id,
            "Points redeemed for booking #{$booking->booking_code}"
        );
    }

    /**
     * Refund points from a cancelled booking
     */
    public function refundBookingPoints(Booking $booking): ?PointLedger
    {
        if (!$booking->user) {
            return null;
        }

        // Find the original points earned from this booking
        $earnedLedger = PointLedger::where('user_id', $booking->user_id)
            ->where('booking_id', $booking->id)
            ->where('type', 'earned')
            ->where('source', 'booking')
            ->first();

        if (!$earnedLedger) {
            return null;
        }

        // Deduct the points as refund
        return $booking->user->deductPoints(
            $earnedLedger->points,
            'booking_refund',
            $booking->id,
            "Points refunded from cancelled booking #{$booking->booking_code}"
        );
    }

    /**
     * Check and update user's membership tier
     */
    public function updateMembershipTier(User $user): void
    {
        // Calculate total lifetime points (all earned points)
        $lifetimePoints = PointLedger::where('user_id', $user->id)
            ->whereIn('type', ['earned', 'refunded'])
            ->sum('points');

        $newTier = MembershipTier::fromLifetimePoints($lifetimePoints);

        if ($user->membership_tier !== $newTier->value) {
            $user->update(['membership_tier' => $newTier->value]);
        }
    }

    /**
     * Expire old points
     */
    public function expirePoints(): int
    {
        $expiredCount = 0;

        DB::transaction(function () use (&$expiredCount) {
            // Find all points that should expire
            $expiringLedgers = PointLedger::where('type', 'earned')
                ->where('expires_at', '<', Carbon::today())
                ->whereNull('expired_at')
                ->get();

            foreach ($expiringLedgers as $ledger) {
                $user = $ledger->user;

                if (!$user) {
                    $ledger->update(['expired_at' => now()]);
                    continue;
                }

                $pointsToExpire = min($ledger->points, $user->points);

                if ($pointsToExpire > 0) {
                    $user->decrement('points', $pointsToExpire);
                    $user->refresh();

                    $user->pointLedgers()->create([
                        'type' => 'expired',
                        'points' => -$pointsToExpire,
                        'balance_after' => $user->points,
                        'source' => 'expiry',
                        'description' => "Points expired from {$ledger->created_at->format('Y-m-d')}",
                    ]);
                }

                // Mark the original ledger as processed
                $ledger->update(['expired_at' => now()]);

                $expiredCount++;
            }
        });

        return $expiredCount;
    }

    /**
     * Get user's point summary
     */
    public function getUserPointSummary(User $user): array
    {
        $tier = MembershipTier::from($user->membership_tier ?? 'silver');

        $lifetimeEarned = PointLedger::where('user_id', $user->id)
            ->whereIn('type', ['earned', 'refunded'])
            ->sum('points');

        $totalUsed = PointLedger::where('user_id', $user->id)
            ->where('type', 'used')
            ->sum('points');

        $totalExpired = PointLedger::where('user_id', $user->id)
            ->where('type', 'expired')
            ->sum('points');

        $expiringInNext30Days = PointLedger::where('user_id', $user->id)
            ->where('type', 'earned')
            ->whereBetween('expires_at', [now(), now()->addDays(30)])
            ->sum('points');

        return [
            'current_balance' => $user->points,
            'lifetime_earned' => $lifetimeEarned,
            'total_used' => abs($totalUsed),
            'total_expired' => abs($totalExpired),
            'expiring_soon' => $expiringInNext30Days,
            'membership_tier' => $tier->value,
            'tier_label' => $tier->label(),
            'tier_benefits' => $tier->getBenefits(),
            'next_tier' => $this->getNextTierInfo($lifetimeEarned),
        ];
    }

    /**
     * Get information about the next membership tier
     */
    private function getNextTierInfo(int $lifetimePoints): ?array
    {
        if ($lifetimePoints >= MembershipTier::PLATINUM->getMinimumPoints()) {
            return null; // Already at max tier
        }

        $nextTier = $lifetimePoints >= MembershipTier::GOLD->getMinimumPoints()
            ? MembershipTier::PLATINUM
            : MembershipTier::GOLD;

        $pointsNeeded = $nextTier->getMinimumPoints() - $lifetimePoints;

        return [
            'tier' => $nextTier->value,
            'label' => $nextTier->label(),
            'points_needed' => $pointsNeeded,
            'minimum_points' => $nextTier->getMinimumPoints(),
        ];
    }
}
