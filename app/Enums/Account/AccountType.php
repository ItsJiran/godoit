<?php

namespace App\Enums\Account;
use App\Traits\BasicEnumTrait; // Assuming this trait exists

enum AccountType: string
{
    use BasicEnumTrait;

    case E_WALLET = 'e-wallet';

    /**
     * Get a displayable name for the account type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::E_WALLET => 'E-Wallet',
            // Add labels for other cases here
        };
    }
}