<?php

namespace App\Services\Acquisition;

use App\Models\User;
use App\Models\Product; // Assuming you have a Product model that defines membership types/durations
use App\Models\UserAcquisition;
use App\Enums\Acquisition\AcquisitionStatus;
use Illuminate\Database\Eloquent\Model; // For sourceable
use Carbon\Carbon;

class UserAcquisitionService
{
    /**
     * Grants a membership to a specific user based on a product.
     *
     * @param User $user The user model to grant the membership to.
     * @param Product $product The product model representing the membership type/level.
     * @param User|null $grantedBy The user (e.g., admin) who manually granted this membership (optional, null for system grants).
     * @param string|null $reason A description of why the membership was granted (optional).
     * @param Carbon|null $endDate An optional specific end date for the membership. If null, product's duration or lifetime assumed.
     * @return UserAcquisition The newly created UserAcquisition instance.
     */
    public static function grantAcquisition(
        User $user,
        Product $product, // Pass the Product model itself if its properties like duration are relevant
        ?Model $sourceable = null, // Pass the Product model itself if its properties like duration are relevant
        ?User $grantedBy = null,
        ?string $reason = null,
        ?Carbon $endDate = null // You might calculate this based on $product
    ): UserAcquisition {
        // Here you can add more complex business logic:
        // - Check if the user already has an active membership and handle upgrades/extensions.
        // - Calculate the $endDate based on the $product's duration (e.g., $product->duration_days).
        // - Dispatch events (e.g., MembershipGranted event) for other parts of the application to react.
        // - Log the membership grant.
        // - Perform any related financial transactions.

        return UserAcquisition::create([
            'user_id' => $user->id,
            'product_id' => $product->id, // Use product ID
            'sourceable_id' => $sourceable ? $sourceable->id : null,
            'sourceable_type' => $sourceable ? $sourceable::class : null,
            'sourceable_description' => $sourceable ? 'Digenerate berdasarkan ' . ' ' . $sourceable::class : null,
            'granted_by_user_id' => $grantedBy ? $grantedBy->id : null,
            'grant_reason' => $reason,
            'status' => AcquisitionStatus::ACTIVE->value,
            'start_date' => now(),
            'end_date' => $endDate, // Or calculated based on product
        ]);
    }

        /**
     * Checks if a user has an active acquisition for a specific product.
     * An acquisition is considered active if its status is 'ACTIVE' and its end_date is either null or in the future.
     *
     * @param User $user The user to check.
     * @param Product $product The product to check for.
     * @return bool True if the user has an active acquisition for the product, false otherwise.
     */
    public static function hasActiveAcquisition(User $user, Product $product): bool
    {
        return UserAcquisition::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('status', AcquisitionStatus::ACTIVE->value)
            ->where(function ($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>', now());
            })
            ->exists();
    }
    
}