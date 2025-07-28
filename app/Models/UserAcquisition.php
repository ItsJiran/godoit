<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasSequentialCode; // Import the trait
use App\Enums\Acquisition\AcquisitionStatus; // Ensure you have this Enum created

class UserAcquisition extends Model
{
    use HasFactory, SoftDeletes; // Use SoftDeletes trait for the deleted_at column

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_acquisitions'; // Explicitly define table name if it's not plural of model name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id', 
        'sourceable_id', 
        'sourceable_type',
        'sourceable_description',
        'granted_by_user_id', // Field for auditability: who manually granted
        'grant_reason',       // Field for auditability: why it was manually granted
        'status',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime', // Cast soft delete timestamp
    ];

    /**
     * Get the user that owns this membership.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the admin user who manually granted this membership (if applicable).
     * This relationship will return null if granted_by_user_id is null (e.g., via purchase).
     */
    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by_user_id');
    }

    /**
     * Scope a query to only include currently active memberships.
     * An "active" membership has the status 'active' and either no end_date (lifetime)
     * or an end_date in the future.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', AcquisitionStatus::ACTIVE)
                     ->where(function ($q) {
                         $q->whereNull('end_date') // No end date (lifetime)
                           ->orWhere('end_date', '>=', now()); // Or end date is in the future
                     });
    }

    /**
     * Check if the membership is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === AcquisitionStatus::ACTIVE &&
               ($this->end_date === null || $this->end_date->isFuture());
    }

    /**
     * Check if the membership has expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->status === AcquisitionStatus::EXPIRED ||
               ($this->end_date !== null && $this->end_date->isPast());
    }


    /**
     * Check if a specific user already has an active acquisition of a given product.
     *
     * @param int $userId The ID of the user to check.
     * @param int $productId The ID of the product to check for.
     * @return bool True if an active acquisition exists for the user and product, false otherwise.
     */
    public static function userHasActiveProductAcquisition(int $userId, int $productId): bool
    {
        return self::where('user_id', $userId)
                   ->where('product_id', $productId)
                   ->active() // Use the 'active' scope defined on this model
                   ->exists(); // Check if any matching record exists
    }
}