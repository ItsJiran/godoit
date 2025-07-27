<?php

namespace App\Enums\Account;

use App\Traits\BasicEnumTrait; // Assuming this trait exists

enum AccountTransactionPurpose : string
{
    use BasicEnumTrait;
    
    // referreal
    case PRODUCT_BROUGHT = 'product_brought';
    case COMMISSION_CREDIT = 'commission_credit';
    case COMMISSION_REVERSAL = 'commission_reversal';

    // regular transaciton
    case WITHDRAWAL = 'withdrawal';
    case DEPOSIT = 'deposit';

    /**
     * Get a displayable name for the account transaction type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::COMMISSION_CREDIT => 'Commission Credit',
            self::COMMISSION_REVERSAL => 'Commission Reversal',
            self::PRODUCT_BROUGHT => 'Product Brought',
            self::WITHDRAWAL => 'Withdrawal',
            self::DEPOSIT => 'Deposit',
        };
    }
}
