<?php

namespace App\Enums\Account;

use App\Traits\BasicEnumTrait; // Assuming this trait exists

enum AccountTransactionType: string
{
    use BasicEnumTrait;
    
    case IN = 'in';
    case OUT = 'out';

    /**
     * Get a displayable name for the transaction direction.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::IN => 'Inflow',
            self::OUT => 'Outflow',
        };
    }
}