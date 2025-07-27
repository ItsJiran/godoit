<?php

// app/Services/ReferralService.php

namespace App\Http\Services\Referral;

use App\Services\Account\TransactionProcessor;

use App\Models\Account;
use App\Models\User;
use App\Models\AccountTransaction;

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
        User $referredUser, // Include for audit/description purposes
        float $baseAmount,
        ?Model $sourceable = null,
        ?string $description = null
    ): AccountTransaction {
        return DB::transaction(function () use (
            $referrerUser,
            $referredUser,
            $baseAmount,
            $sourceable,
            $description,
        ) {
            // 1. Determine the commission amount
            $commissionAmount = self::calculateReferralCommission($baseAmount);

            if ($commissionAmount <= 0) {
                // If commission is 0 or negative, don't create a transaction
                throw new \Exception("Calculated referral commission amount is zero or negative.");
            }

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
                description: $description ?? "Referral commission from " . $referredUser->name . " (ID: " . $referredUser->id . ")",
                sourceable: $sourceable
            );

            return $referralTransaction;
        });
    }

    /**
     * Processes all pending AccountTransactions related to a given sourceable model.
     * This method updates their status (e.g., to COMPLETED or CANCELLED)
     * and triggers the corresponding balance updates via TransactionProcessor.
     *
     * @param Model $sourceable The originating model (e.g., Order, Payment) whose status dictates transaction processing.
     * @param AccountTransactionStatus $targetStatus The desired final status for related pending transactions (e.g., COMPLETED, CANCELLED, FAILED).
     * @return bool True if all relevant transactions were processed successfully.
     * @throws \Exception If any transaction processing fails, the entire batch will roll back.
     */
    public static function processSourceableTransactions(
        Model $sourceable,
        AccountTransactionStatus $targetStatus
    ): bool {
        // Wrap the entire batch operation in a single database transaction.
        // If any individual transaction processing fails, the entire batch will be rolled back.
        return DB::transaction(function () use ($sourceable, $targetStatus) {
            // Fetch all pending transactions associated with this sourceable
            $pendingTransactions = AccountTransaction::getTransactionsBySourceable($sourceable)
                                                    ->where('status', AccountTransactionStatus::PENDING->value);

            foreach ($pendingTransactions as $transaction) {
                if ($targetStatus === AccountTransactionStatus::COMPLETED) {
                    // If the sourceable (e.g., payment) is valid/completed, process the commission as completed
                    TransactionProcessor::completedTransaction($transaction);
                } elseif ($targetStatus === AccountTransactionStatus::CANCELLED || $targetStatus === AccountTransactionStatus::FAILED) {
                    // If the sourceable (e.g., payment) failed or was cancelled, cancel the pending commission
                    TransactionProcessor::failedTransaction($transaction);
                } else {
                    // Handle other target statuses if necessary, or throw an exception
                    throw new \InvalidArgumentException("Unsupported target status '{$targetStatus->value}' for processing pending transactions.");
                }
            }
            return true; // All relevant transactions processed successfully
        });
    }

    /**
     * Calculates the referral commission amount based on a base amount.
     * This logic should be externalized (e.g., config, database settings).
     *
     * @param float $baseAmount The base amount (e.g., purchase value).
     * @return float The calculated commission amount.
     */
    public static function calculateReferralCommission(float $baseAmount): float
    {
        // TODO: Implement your actual commission logic here.
        // This could come from a settings table, a config file, or be tiered.
        $referralRate = config('referral.commission_rate', 0.10); // Example: 10% commission
        return round($baseAmount * $referralRate, 2); // Round to 2 decimal places
    }

    /**
     * You might also add methods for different referral types, e.g.,
     * generateRegistrationBonus(User $referredUser, ...), etc.
     */
}