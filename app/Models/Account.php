<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Import the SoftDeletes trait
use Illuminate\Support\Facades\DB; // For database transactions

class Account extends Model
{
    use HasFactory, SoftDeletes; // Use the SoftDeletes trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'account_number',
        'account_type',
        'currency',
        'balance',
        // 'version', // Make sure 'version' is fillable for optimistic locking updates
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'decimal:2', // Cast balance to decimal with 2 decimal places
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime', // Cast deleted_at to datetime
    ];

    /**
     * Get the user that owns the account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // create a method to get account user by type and if not exist create new one by its account purpose
    /**
     * Retrieve an account for a specific user and type, or create a new one if it doesn't exist.
     *
     * @param int $userId The ID of the user.
     * @param string $accountType The type of account (e.g., 'main', 'commission', 'bonus').
     * @return \App\Models\Account The found or newly created Account model instance.
     */
    public static function getAccountUserByType(int $userId, string $accountType): self
    {
        // Attempt to find an existing account for the given user and type
        $account = self::where('user_id', $userId)
                       ->where('account_type', $accountType)
                       ->first();

        // If an account is found, return it
        if ($account) {
            return $account;
        }

        // If no account exists, create a new one
        return self::create([
            'user_id' => $userId,
            // 'account_number' => self::generateUniqueAccountNumber(), // Generate a unique account number
            'account_type' => $accountType,
            'currency' => config('app.default_currency', 'IDR'), // Use a default currency from config or 'IDR'
            'balance' => 0, // New accounts start with a zero balance
            'status' => 'active', // Default status for new accounts
        ]);
    }

    /**
     * Credits the account balance.
     * This operation adds funds to the account.
     *
     * @param float $amount The amount to add.
     * @return bool True if successful, false otherwise.
     * @throws \Exception If the amount is not positive.
     */
    public function credit(float $amount): bool
    {
        if ($amount <= 0) {
            throw new \Exception("Credit amount must be positive.");
        }

        return DB::transaction(function () use ($amount) {
            // Lock the account row to prevent race conditions during update
            $this->lockForUpdate();

            $this->balance += $amount;
            return $this->save();
        });
    }

    /**
     * Debits the account balance.
     * This operation subtracts funds from the account.
     *
     * @param float $amount The amount to subtract.
     * @return bool True if successful, false otherwise.
     * @throws \Exception If the amount is not positive or insufficient funds.
     */
    public function debit(float $amount): bool
    {
        if ($amount <= 0) {
            throw new \Exception("Debit amount must be positive.");
        }

        return DB::transaction(function () use ($amount) {
            // Lock the account row to prevent race conditions during update
            $this->lockForUpdate();

            if ($this->balance < $amount) {
                throw new \Exception("Insufficient funds for debit transaction.");
            }

            $this->balance -= $amount;
            return $this->save();
        });
    }
}