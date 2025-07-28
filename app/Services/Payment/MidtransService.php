<?php

namespace App\Services\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

/**
 * Service to handle all interactions with the Midtrans payment gateway using static methods.
 */
class MidtransService
{
    /**
     * Initializes Midtrans configuration.
     * This method should be called once, for example, in a service provider's boot method
     * or at the beginning of any static method that requires Midtrans configuration.
     */
    private static function initializeConfig(): void
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Generates a Midtrans Snap Token for a given set of transaction parameters.
     *
     * @param array $params An array containing 'transaction_details', 'customer_details', and 'item_details'.
     * @return string The generated Snap Token.
     * @throws \Exception If there's an error generating the Snap Token.
     */
    public static function generateSnapToken(array $params): string
    {
        self::initializeConfig(); // Ensure configuration is set

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            Log::error("Midtrans Snap Token generation failed: " . $e->getMessage(), ['params' => $params]);
            throw new \Exception("Failed to generate payment token: " . $e->getMessage());
        }
    }

    /**
     * Handles the incoming Midtrans notification.
     *
     * @param Request $request The incoming notification request from Midtrans.
     * @return \Midtrans\Notification The Midtrans Notification object.
     * @throws \Exception If there's an error processing the notification.
     */
    public static function handleNotification(Request $request): Notification
    {
        self::initializeConfig(); // Ensure configuration is set

        try {
            $notification = new Notification();
            return $notification;
        } catch (\Exception $e) {
            Log::error("Midtrans notification handling failed: " . $e->getMessage(), ['request_data' => $request->all()]);
            throw new \Exception("Failed to process Midtrans notification: " . $e->getMessage());
        }
    }

    /**
     * Retrieves the status of a transaction directly from Midtrans using the order ID.
     * This method is useful for verifying transaction status if a notification is missed or for manual checks.
     *
     * @param string $orderId The order ID to check.
     * @return object The transaction status object from Midtrans.
     * @throws \Exception If there's an error fetching the status.
     */
    public static function getTransactionStatus(string $orderId): object
    {
        self::initializeConfig(); // Ensure configuration is set

        try {
            $status = \Midtrans\Transaction::status($orderId);
            return $status;
        } catch (\Exception $e) {
            Log::error("Failed to get Midtrans transaction status for order ID {$orderId}: " . $e->getMessage());
            throw new \Exception("Failed to retrieve transaction status: " . $e->getMessage());
        }
    }
}