<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Enums\Account\AccountTransactionType; // Import the enum you specified for 'type'
use App\Enums\Account\AccountTransactionPurpose; // Import the purpose enum
use App\Enums\Account\AccountTransactionStatus; // Import the status enum

class AccountTransaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * IMPORTANT: This fillable array strictly adheres to your last provided code.
     * Fields like 'related_order_id', 'referred_user_id', and 'effective_at' from the migration
     * are not included here as they were not in your specified fillable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'account_id',
        'amount',
        'type', // As per your provided fillable, used for direction (e.g., IN/OUT)
        'purpose',
        'sourceable_id',
        'sourceable_type',
        'description',
        'status',
        'effective_at', // Included as per your provided fillable
    ];

    /**
     * The attributes that should be cast.
     *
     * IMPORTANT: This casts array strictly adheres to your last provided code.
     * 'effective_at' casting is added based on its presence in fillable.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'type' => AccountTransactionType::class, // As per your provided casts, used for direction
        'purpose' => AccountTransactionPurpose::class,
        'status' => AccountTransactionStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account that owns the transaction.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the sourceable model that this transaction belongs to.
     */
    public function sourceable()
    {
        return $this->morphTo();
    }

    // Note: 'relatedOrder' and 'referredUser' relationships are omitted here
    // because 'related_order_id' and 'referred_user_id' were not in your last provided $fillable array.

    /**
     * Create a new account transaction record.
     *
     * This method is generated based on the fillable properties you provided.
     * It does not include 'related_order_id' or 'referred_user_id' in its parameters
     * or data payload, as they were not in the provided $fillable array.
     *
     * @param int $userId The ID of the user whose account is affected.
     * @param float $amount The amount of the transaction.
     * @param \App\Enums\Account\AccountTransactionType $direction The direction of the transaction (e.g., IN, OUT).
     * @param \App\Enums\Account\AccountTransactionPurpose $purpose The purpose of the transaction (e.g., commission_credit, withdrawal).
     * @param \App\Enums\Account\AccountTransactionStatus $status The current status of the transaction (e.g., pending, completed).
     * @param string|null $description A description for the transaction.
     * @param \Illuminate\Database\Eloquent\Model|null $sourceable The optional source model (for polymorphic relation).
     * @return static The newly created AccountTransaction instance.
     */
    public static function createTransaction(
        int $userId,
        int $accountId,
        float $amount,
        AccountTransactionType $direction, // Using the enum name you provided for 'type'
        AccountTransactionPurpose $purpose,
        AccountTransactionStatus $status,
        ?string $description = null,
        ?Model $sourceable = null
    ): self {
        $data = [
            'user_id' => $userId,
            'account_id' => $accountId,
            'amount' => $amount,
            'type' => $direction, // Mapping to 'type' as per your fillable
            'purpose' => $purpose,
            'description' => $description,
            'status' => $status,
        ];

        // Handle polymorphic relationship
        if ($sourceable) {
            $data['sourceable_id'] = $sourceable->getKey();
            $data['sourceable_type'] = $sourceable->getMorphClass();
        }

        return self::create($data);
    }

    /**
     * Retrieve account transactions based on provided filters.
     *
     * This method provides a flexible way to query transactions.
     * Example Usage:
     * // Get all completed commission credits for a specific user
     * $transactions = AccountTransaction::getTransactions([
     * 'user_id' => 1,
     * 'purpose' => AccountTransactionPurpose::COMMISSION_CREDIT,
     * 'status' => AccountTransactionStatus::COMPLETED
     * ]);
     *
     * // Get a single transaction by its ID
     * $transaction = AccountTransaction::getTransactions(['id' => 123], true);
     *
     * @param array $filters An associative array of filters (e.g., ['user_id' => 1, 'purpose' => AccountTransactionPurpose::COMMISSION_CREDIT]).
     * @param bool $singleResult If true, return the first matching transaction, otherwise return a collection.
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\AccountTransaction|null
     */
    public static function getTransactions(array $filters = [], bool $singleResult = false)
    {
        $query = self::query();

        foreach ($filters as $key => $value) {
            // Handle enum values correctly for querying
            if ($value instanceof \UnitEnum) { // For PHP 8.1+ Enums
                $query->where($key, $value->value);
            } else {
                $query->where($key, $value);
            }
        }

        if ($singleResult) {
            return $query->first();
        }

        return $query->get();
    }

    /**
     * Retrieve all account transactions associated with a given sourceable model.
     *
     * @param \Illuminate\Database\Eloquent\Model $sourceable The sourceable model (e.g., an Order, a User model for registration bonus).
     * @return \Illuminate\Support\Collection A collection of AccountTransaction instances.
     */
    public static function getTransactionsBySourceable(Model $sourceable): Collection
    {
        return self::where('sourceable_id', $sourceable->getKey())
                   ->where('sourceable_type', $sourceable->getMorphClass())
                   ->get();
    }

    /**
     * Retrieve a single account transaction by its ID.
     *
     * @param int $id The ID of the transaction to retrieve.
     * @return static|null The AccountTransaction instance if found, or null otherwise.
     */
    public static function findTransactionById(int $id): ?self
    {
        return self::find($id);
    }

}