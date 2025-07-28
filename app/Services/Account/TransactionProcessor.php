<?php

// app/Services/TransactionProcessor.php

namespace App\Services\Account;

use App\Models\AccountTransaction;
use App\Enums\Account\AccountTransactionStatus;
use App\Enums\Account\AccountTransactionType;
use Illuminate\Support\Facades\DB; // Make sure to import DB facade

class TransactionProcessor
{
    /**
     * Completes a pending account transaction and updates the account balance.
     *
     * @param AccountTransaction $transaction The transaction to complete.
     * @return bool True if successful, false otherwise.
     * @throws \Exception If the transaction is not pending or an unknown type.
     */
    public static function completedTransaction(AccountTransaction $transaction): bool
    {
        if ($transaction->status !== AccountTransactionStatus::PENDING) {
            throw new \Exception("Transaction ID {$transaction->id}: Only pending transactions can be completed by this method.");
        }

        // Note: DB::transaction here will create a savepoint if called within an outer transaction.
        return DB::transaction(function () use ($transaction) {
            // Update the transaction status first
            $transaction->status = AccountTransactionStatus::COMPLETED;
            $transaction->save();

            $account = $transaction->account; // Ensure the Account relationship is loaded or accessible

            // Apply the transaction amount to the account balance
            if ($transaction->type === AccountTransactionType::IN) {
                // Credit the account if it's an 'in' type transaction
                $account->credit($transaction->amount);
            } elseif ($transaction->type === AccountTransactionType::OUT) {
                // Debit the account if it's an 'out' type transaction
                $account->debit($transaction->amount);
            } else {
                // Throw an exception for unexpected transaction types
                throw new \Exception("Transaction ID {$transaction->id}: Unknown transaction type '{$transaction->type->value}' for balance update.");
            }

            return true;
        });
    }



    /**
     * Fails a pending account transaction.
     * This marks the transaction as FAILED without affecting the account balance,
     * as the balance would not have been adjusted for a pending transaction.
     *
     * @param AccountTransaction $transaction The transaction to mark as failed.
     * @return bool True if successful, false otherwise.
     * @throws \Exception If the transaction is not in a status that can be failed (e.g., not PENDING).
     */
    public static function failedTransaction(AccountTransaction $transaction): bool
    {
        // Only pending transactions can typically transition to FAILED.
        // If a transaction has already been completed, it would need to be reversed/refunded, not simply 'failed'.
        if ($transaction->status !== AccountTransactionStatus::PENDING) {
            throw new \Exception("Transaction ID {$transaction->id}: Only pending transactions can be marked as failed by this method.");
        }

        return DB::transaction(function () use ($transaction) {
            $transaction->status = AccountTransactionStatus::FAILED;
            $transaction->save();
            // No balance adjustment needed as this was a pending transaction that never affected the balance.
            return true;
        });
    }

    /**
     * Reverses a completed account transaction and adjusts the account balance.
     *
     * @param AccountTransaction $transaction The transaction to reverse.
     * @return bool True if successful, false otherwise.
     * @throws \Exception If the transaction is not completed or an unknown type.
     */
    public static function reverseTransaction(AccountTransaction $transaction): bool
    {
        if ($transaction->status !== AccountTransactionStatus::COMPLETED) {
            throw new \Exception("Transaction ID {$transaction->id}: Only completed transactions can be reversed.");
        }

        // Note: DB::transaction here will create a savepoint if called within an outer transaction.
        return DB::transaction(function () use ($transaction) {
            // Update transaction status to reversed
            $transaction->status = AccountTransactionStatus::REVERSED;
            $transaction->save();

            $account = $transaction->account; // Ensure the Account relationship is loaded or accessible

            // Reverse the balance based on the original transaction type
            if ($transaction->type === AccountTransactionType::IN) {
                // If original was 'in', now subtract from account
                $account->debit($transaction->amount);
            } elseif ($transaction->type === AccountTransactionType::OUT) {
                // If original was 'out', now add back to account
                $account->credit($transaction->amount);
            } else {
                // Throw an exception for unexpected transaction types
                throw new \Exception("Transaction ID {$transaction->id}: Unknown transaction type '{$transaction->type->value}' for balance reversal.");
            }

            return true;
        });
    }

    /**
     * Processes a collection of account transactions (either completing or reversing them).
     * This method ensures that all operations within the batch are atomic.
     *
     * @param \Illuminate\Support\Collection|\App\Models\AccountTransaction[] $transactions A collection or array of AccountTransaction models.
     * @param string $action The action to perform: 'complete' or 'reverse'.
     * @return bool True if all transactions were processed successfully.
     * @throws \InvalidArgumentException If an invalid action is provided.
     * @throws \Exception If any individual transaction processing fails, the entire batch will roll back.
     */
    public static function processMultipleTransactions(
        $transactions,
        string $action
    ): bool {
        // Validate the action
        if (!in_array($action, ['complete', 'reverse'])) {
            throw new \InvalidArgumentException("Invalid action: '{$action}'. Must be 'complete' or 'reverse'.");
        }

        // Wrap the entire batch operation in a single database transaction.
        // If any individual transaction processing fails, the entire batch will be rolled back.
        return DB::transaction(function () use ($transactions, $action) {
            foreach ($transactions as $transaction) {
                if (!$transaction instanceof AccountTransaction) {
                    throw new \InvalidArgumentException("All items in the transactions collection must be instances of AccountTransaction.");
                }

                if ($action === 'complete') {
                    // Call the existing static method to complete the transaction
                    self::completedTransaction($transaction);
                } elseif ($action === 'reverse') {
                    // Call the existing static method to reverse the transaction
                    self::reverseTransaction($transaction);
                }
            }
            return true; // All transactions processed successfully
        });
    }
}