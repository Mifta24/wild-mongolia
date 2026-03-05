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
            $booking->user->syncMembershipStatus();
            $tier = $booking->user->membershipTier();
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

        return DB::transaction(function () use ($booking) {
            $existingLedger = PointLedger::where('user_id', $booking->user_id)
                ->where('booking_id', $booking->id)
                ->where('type', 'earned')
                ->where('source', 'booking')
                ->first();

            if ($existingLedger) {
                return $existingLedger;
            }

            $lockedUser = User::query()
                ->whereKey($booking->user_id)
                ->lockForUpdate()
                ->first();

            if (!$lockedUser) {
                return null;
            }

            $lockedUser->syncMembershipStatus();

            $basePoints = (int) floor($booking->total_price / 100);
            $points = max(1, (int) floor($basePoints * $lockedUser->membershipTier()->getPointMultiplier()));

            return $lockedUser->addPoints(
                $points,
                'booking',
                $booking->id,
                "Points from booking #{$booking->booking_code}"
            );
        });
    }

    /**
     * Award cashback points after successful membership purchase or renewal.
     */
    public function awardMembershipCashback(
        User $user,
        MembershipTier $tier,
        string $action = 'subscribe',
        ?string $sessionId = null
    ): ?PointLedger {
        if (!$tier->isPaidPlan()) {
            return null;
        }

        $cashbackPoints = $tier->getCashbackPoints();

        if ($cashbackPoints <= 0) {
            return null;
        }

        $description = sprintf(
            'Membership cashback for %s (%s)%s',
            $tier->label(),
            $action,
            $sessionId ? ' [session:' . $sessionId . ']' : ''
        );

        return DB::transaction(function () use ($user, $cashbackPoints, $description) {
            $existingLedger = PointLedger::query()
                ->where('user_id', $user->id)
                ->where('type', 'earned')
                ->where('source', 'membership_cashback')
                ->where('description', $description)
                ->first();

            if ($existingLedger) {
                return $existingLedger;
            }

            $lockedUser = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedUser) {
                return null;
            }

            return $lockedUser->addPoints(
                $cashbackPoints,
                'membership_cashback',
                null,
                $description
            );
        });
    }

    /**
     * Redeem points for discount
     */
    public function redeemPoints(User $user, int $points): float
    {
        $user->syncMembershipStatus();

        if ($user->points < $points) {
            throw new \Exception('Insufficient points');
        }

        // 100 points = 100 THB discount
        $discount = $points;

        // Apply tier bonus
        $tier = $user->membershipTier();
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
        $user->syncMembershipStatus();
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
        $user->syncMembershipStatus();
        $tier = $user->membershipTier();

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
            'next_tier' => $this->getNextTierInfo($tier),
        ];
    }

    /**
     * Get information about the next membership tier
     */
    private function getNextTierInfo(MembershipTier $currentTier): ?array
    {
        $nextTier = match ($currentTier) {
            MembershipTier::SILVER => MembershipTier::GOLD,
            MembershipTier::GOLD => MembershipTier::PLATINUM,
            MembershipTier::PLATINUM => null,
        };

        if (!$nextTier) {
            return null;
        }

        return [
            'tier' => $nextTier->value,
            'label' => $nextTier->label(),
            'points_needed' => 0,
            'minimum_points' => 0,
            'note' => 'Upgrade requires subscription activation.',
        ];
    }
}
