<?php

// app/Services/TransactionProcessor.php

namespace App\Services\Account;

use App\Models\Account;
use App\Models\User;
use App\Models\AccountTransaction;

use App\Enums\Account\AccountTransactionStatus;
use App\Enums\Account\AccountTransactionPurpose;
use App\Enums\Account\AccountTransactionType;
use App\Enums\Account\AccountType;

use Illuminate\Support\Facades\DB; // Make sure to import DB facade
use Illuminate\Database\Eloquent\Model; // For sourceable

class TransactionService
{
   /**
     * Generates and processes a referral commission transaction for a referrer.
     *
     * This method orchestrates the creation of an AccountTransaction
     * for a referral and then processes it to update the referrer's account balance.
     *
     * @param float $baseAmount The base amount from which the commission is calculated (e.g., referred user's first purchase amount).
     * @param Model|null $sourceable The originating model that triggered the commission (e.g., Order, Registration).
     * @param string|null $description An optional description for the transaction.
     * @return AccountTransaction The newly created and processed referral transaction.
     * @throws \Exception If the referrer's account cannot be found/created or transaction processing fails.
     */
    public static function generateTransactionCheckout(
        ?User $customer = null, // Include for audit/description purposes
        float $baseAmount,
        ?Model $sourceable = null,
        ?string $description = null
    ): AccountTransaction {
        return DB::transaction(function () use (
            $customer,
            $baseAmount,
            $sourceable,
            $description,
        ) {
            $adminUser = User::where('role','admin')->first();

            // 2. Get the first admin container commission account
            // Assuming you have a specific AccountType for referral commissions
            $adminAccount = Account::getAccountUserByType(
                $adminUser->id,
                AccountType::E_WALLET->value 
            );

            // 3. Create the pending AccountTransaction for the referrer
            $mainTransaction = AccountTransaction::createTransaction(
                userId: $adminUser->id,
                accountId: $adminAccount->id,
                amount: $baseAmount,
                direction: AccountTransactionType::IN, // Commission is a credit to the referrer
                purpose: AccountTransactionPurpose::PRODUCT_BROUGHT, // Or COMMISSION_CREDIT
                status: AccountTransactionStatus::PENDING, // Start as pending
                description: $description ?? "Product brought from " . ($customer ? $customer->name : 'Guest') . " (ID: " . ($customer ? $customer->id : 'No-ID') . ")",
                sourceable: $sourceable
            );

            return $mainTransaction;
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
}