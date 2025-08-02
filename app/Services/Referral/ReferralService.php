<?php

// app/Services/ReferralService.php

namespace App\Services\Referral;

use App\Services\Account\TransactionProcessor;

use App\Models\Account;
use App\Models\User;
use App\Models\AccountTransaction;

use App\Models\Setting;

use App\Enums\Account\AccountTransactionType;
use App\Enums\Account\AccountTransactionPurpose;
use App\Enums\Account\AccountTransactionStatus;
use App\Enums\Account\AccountType; // Make sure this is imported if used

use Illuminate\Database\Eloquent\Model; // For sourceable
use Illuminate\Support\Facades\DB; // For database transactions

class ReferralService
{

    /**
     * Generates and processes a referral commission transaction for a referrer.
     *
     * This method orchestrates the creation of an AccountTransaction
     * for a referral and then processes it to update the referrer's account balance.
     *
     * @param User $referrerUser The user who made the referral.
     * @param User $referredUser The user who was referred (for context/logging).
     * @param float $baseAmount The base amount from which the commission is calculated (e.g., referred user's first purchase amount).
     * @param Model|null $sourceable The originating model that triggered the commission (e.g., Order, Registration).
     * @param string|null $description An optional description for the transaction.
     * @return AccountTransaction The newly created and processed referral transaction.
     * @throws \Exception If the referrer's account cannot be found/created or transaction processing fails.
     */
    public static function generateReferralCommission(
        User $referrerUser,
        ?User $referredUser = null, // Include for audit/description purposes
        float $commissionAmount,
        $comissionPercentage,
        ?Model $sourceable = null,
        ?string $description = null
    ): AccountTransaction {
        return DB::transaction(function () use (
            $referrerUser,
            $referredUser,
            $commissionAmount,
            $comissionPercentage,
            $sourceable,
            $description,
        ) {


            // // generate refferer for the parent if parent referrer is premium..
            // if ( $parent_referrer != null && $parent_referrer->activeMembershipPremium() ) {
            //     $comission_percentage_premium_downline = Setting::where('slug','premium_downline');
            //     $commissionAmountDownline = round($commissionAmount * ($comission_percentage_premium_downline->value / 100), 2);
            //     $commissionAmount -= $commissionAmountDownline;

            //     $parentReferrerAccount = Account::getAccountUserByType(
            //         $parent_referrer->id,
            //         AccountType::E_WALLET->value 
            //     );

            //     $referralTransaction = AccountTransaction::createTransaction(
            //         userId: $referrerUser->id,
            //         accountId: $referrerAccount->id,
            //         amount: $commissionAmount,
            //         direction: AccountTransactionType::IN, // Commission is a credit to the referrer
            //         purpose: AccountTransactionPurpose::COMMISSION_CREDIT, // Or COMMISSION_CREDIT
            //         status: AccountTransactionStatus::PENDING, // Start as pending
            //         description: $description ?? "Referral commission from " . ($referredUser ? $referredUser->name : 'Guest') . " (ID: " . ($referredUser ? $referredUser->id : 'No-ID') . ")",
            //         sourceable: $sourceable
            //     );
            // }

            // 2. Get the referrer's commission account
            // Assuming you have a specific AccountType for referral commissions
            $referrerAccount = Account::getAccountUserByType(
                $referrerUser->id,
                AccountType::E_WALLET->value 
            );

            // 3. Create the pending AccountTransaction for the referrer
            $referralTransaction = AccountTransaction::createTransaction(
                userId: $referrerUser->id,
                accountId: $referrerAccount->id,
                amount: $commissionAmount,
                direction: AccountTransactionType::IN, // Commission is a credit to the referrer
                purpose: AccountTransactionPurpose::COMMISSION_CREDIT, // Or COMMISSION_CREDIT
                status: AccountTransactionStatus::PENDING, // Start as pending
                description: $description ?? "Referral commission " . $comissionPercentage . '% ' . " from " . ($referredUser ? $referredUser->name : 'Guest') . " (ID: " . ($referredUser ? $referredUser->id : 'No-ID') . ")",
                sourceable: $sourceable
            );

            return $referralTransaction;
        });
    }

    /**
     * Calculates the referral commission amount based on a base amount.
     * This logic should be externalized (e.g., config, database settings).
     *
     * @param float $baseAmount The base amount (e.g., purchase value).
     * @return float The calculated commission amount.
     */
    public static function calculateReferralCommission(float $baseAmount, User $referrerUser): float
    {
        // TODO: Implement your actual commission logic here.
        // This could come from a settings table, a config file, or be tiered.

        $referralRate = config('referral.commission_rate', 0.05); // Example: 10% commission

        // if ( $reffererUser->activeMembershipPremium() )
        //     $referralRate

        return round($baseAmount * $referralRate, 2); // Round to 2 decimal places
    }

    /**
     * You might also add methods for different referral types, e.g.,
     * generateRegistrationBonus(User $referredUser, ...), etc.
     */
}