<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasSequentialCode; // Import the trait
use App\Enums\MembershipStatus; // Ensure you have this Enum created

class UserMembership extends Model
{
    use HasFactory, SoftDeletes; // Use SoftDeletes trait for the deleted_at column

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_memberships'; // Explicitly define table name if it's not plural of model name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
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
        return $query->where('status', MembershipStatus::Active)
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
        return $this->status === MembershipStatus::Active &&
               ($this->end_date === null || $this->end_date->isFuture());
    }

    /**
     * Check if the membership has expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->status === MembershipStatus::Expired ||
               ($this->end_date !== null && $this->end_date->isPast());
    }
}