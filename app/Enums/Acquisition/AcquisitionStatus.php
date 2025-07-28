<?php

namespace App\Enums\Acquisition; // Recommended namespace for Enums in Laravel/PHP applications

use App\Traits\BasicEnumTrait; 
/**
 * Enum MembershipStatus
 *
 * Defines the possible statuses for a user's membership.
 *
 * @package App\Enums
 */
enum AcquisitionStatus: string
{   
    use BasicEnumTrait;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case TRIAL = 'trial';
    case PAUSED = 'paused';

    /**
     * Returns a human-readable label for the membership status.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::CANCELLED => 'Cancelled',
            self::EXPIRED => 'Expired',
            self::TRIAL => 'Trial',
            self::PAUSED => 'Paused',
        };
    }

    /**
     * Checks if the current status indicates an active membership.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE || $this === self::TRIAL;
    }

    /**
     * Checks if the current status indicates a non-active membership.
     *
     * @return bool
     */
    public function isInactive(): bool
    {
        return $this === self::INACTIVE || $this === self::CANCELLED || $this === self::EXPIRED || $this === self::REFUNDED;
    }
}