<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\UserAcquisition;
use App\Models\Membership; // Assuming Membership is a productable type
use App\Enums\Order\OrderStatus;
use App\Enums\Acquisition\AcquisitionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service to handle the completion of orders and creation of user acquisitions.
 */
class OrderService
{
    /**
     * Check if the user already has an active (pending) order for the given product.
     *
     * @param int $userId The ID of the user.
     * @param int $productId The ID of the product.
     * @return bool
     */
    public static function hasPendingOrderForProduct(int $userId, int $productId): bool
    {
        return Order::where('user_id', $userId)
            ->where('status', OrderStatus::PENDING->value)
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();
    }
}