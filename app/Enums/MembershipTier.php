<?php

namespace App\Enums;

enum MembershipTier: string
{
    case SILVER = 'silver';
    case GOLD = 'gold';
    case PLATINUM = 'platinum';

    /**
     * Get point multiplier for the tier
     */
    public function getPointMultiplier(): float
    {
        return match($this) {
            self::SILVER => 1.0,
            self::GOLD => 1.5,
            self::PLATINUM => 2.0,
        };
    }

    /**
     * Get minimum points required for this tier
     */
    public function getMinimumPoints(): int
    {
        return match($this) {
            self::SILVER => 0,
            self::GOLD => 5000,
            self::PLATINUM => 15000,
        };
    }

    /**
     * Get tier benefits
     */
    public function getBenefits(): array
    {
        return match($this) {
            self::SILVER => [
                'Point Multiplier: 1x',
                'Standard booking priority',
                'Basic customer support',
            ],
            self::GOLD => [
                'Point Multiplier: 1.5x',
                'Priority booking',
                'Priority customer support',
                '10% bonus on point redemption',
            ],
            self::PLATINUM => [
                'Point Multiplier: 2x',
                'VIP booking priority',
                'Dedicated customer support',
                '20% bonus on point redemption',
                'Exclusive special offers',
            ],
        };
    }

    /**
     * Get the tier from total lifetime points
     */
    public static function fromLifetimePoints(int $points): self
    {
        if ($points >= self::PLATINUM->getMinimumPoints()) {
            return self::PLATINUM;
        }

        if ($points >= self::GOLD->getMinimumPoints()) {
            return self::GOLD;
        }

        return self::SILVER;
    }

    /**
     * Get display label
     */
    public function label(): string
    {
        return match($this) {
            self::SILVER => 'Silver Member',
            self::GOLD => 'Gold Member',
            self::PLATINUM => 'Platinum Member',
        };
    }

    /**
     * Get badge color
     */
    public function color(): string
    {
        return match($this) {
            self::SILVER => 'gray',
            self::GOLD => 'yellow',
            self::PLATINUM => 'purple',
        };
    }
}
