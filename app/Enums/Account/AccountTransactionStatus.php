<?php

namespace App\Enums\Account;

use App\Traits\BasicEnumTrait; // Assuming this trait exists

enum AccountTransactionStatus: string
{
    use BasicEnumTrait;

    case PENDING = 'pending'; // Initial state for many transactions (e.g., commissions awaiting validation, withdrawals awaiting processing)
    case COMPLETED = 'completed'; // Transaction successfully processed and reflected in balance
    case FAILED = 'failed';     // Transaction attempted but failed (e.g., withdrawal to invalid bank account)
    case CANCELLED = 'cancelled'; // Transaction was pending but was explicitly cancelled (e.g., user cancelled withdrawal, commission cancelled due to refund)
    case REVERSED = 'reversed'; // Transaction was completed but later undone (e.g., clawback of a paid commission)

    /**
     * Get a displayable name for the account transaction status.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::REVERSED => 'Reversed',
            // Add labels for other cases here
        };
    }
}