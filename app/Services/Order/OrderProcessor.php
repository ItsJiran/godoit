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
class OrderProcessor
{
    /**
     * Completes a given order and creates UserAcquisition records for each product item.
     *
     * @param Order $order The order to be completed.
     * @return bool True if the order was successfully completed, false otherwise.
     */
    public static function completeOrder(Order $order): bool
    {
        // Prevent processing if the order is already completed
        if ($order->status === OrderStatus::COMPLETED->value) {
            Log::info("Order {$order->id} is already completed. Skipping processing.");
            return true; // Already in desired state
        }

        // Ensure the order is in a state that can be completed (e.g., PENDING)
        if ($order->status !== OrderStatus::PENDING->value) {
            Log::warning("Attempted to complete order {$order->id} which is not in PENDING status. Current status: {$order->status}");
            return false; // Cannot complete an order that's not pending
        }

        DB::beginTransaction();
        try {
            // 1. Update the Order status to COMPLETED
            $order->status = OrderStatus::COMPLETED->value;
            $order->save();

            // 2. Create UserAcquisition records for all order items
            foreach ($order->orderItems as $orderItem) {
                $product = $orderItem->product; // Get the associated product

                // Determine end_date based on product type (e.g., Membership duration)
                $endDate = null;
                if ($product && $product->productable instanceof Membership) {
                    // If the product is a membership and has a duration
                    if ($product->productable->duration_days) {
                        $endDate = Carbon::now()->addDays($product->productable->duration_days);
                    }
                    // If duration_days is null or 0, it's a lifetime membership, so $endDate remains null
                }
                // Add logic for other productable types if they have expiry dates

                UserAcquisition::create([
                    'user_id' => $order->user_id,
                    'product_id' => $product->id,
                    'status' => AcquisitionStatus::ACTIVE->value,
                    'start_date' => now(),
                    'end_date' => $endDate,
                    'sourceable_id' => $orderItem->id, // Link to the order item
                    'sourceable_type' => get_class($orderItem), // Store the class name
                    'sourceable_description' => "Acquired via Order #{$order->id}, Item #{$orderItem->id}",
                    // 'granted_by_user_id' and 'grant_reason' could be set here if this completion is a "grant"
                    // but typically, it's a "purchase" so these might be null.
                ]);
            }

            DB::commit();
            Log::info("Order {$order->id} successfully completed and user acquisitions created.");
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to complete order {$order->id} and create user acquisitions: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return false;
        }
    }
}