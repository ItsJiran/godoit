<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Services\Referral\ReferralService; // Import the static MidtransService
use App\Services\Payment\MidtransService; // Import the static MidtransService
use App\Services\Account\TransactionService; // Import the static MidtransService

/**
 * Service to handle the creation and processing of payment data.
 */
class PaymentService
{
    /**
     * Generates a payment record and obtains a Midtrans Snap Token for a given order.
     *
     * @param Order $order The order for which to generate the payment.
     * @param Request $request The incoming HTTP request, containing customer details.
     * @return Payment The created Payment model with the Snap Token.
     * @throws \Exception If payment token generation fails.
     */
    public static function generatePaymentForOrder(Order $order, Request $request): Payment
    {
        $user = Auth::user(); // Get the authenticated user

        // Prepare transaction details for Midtrans
        $transactionDetails = [
            'order_id' => $order->slug, // Use order slug as Midtrans order ID
            'gross_amount' => (int) $order->total_price, // Ensure integer for Midtrans
        ];

        // Prepare customer details for Midtrans
        $customerDetails = [
            'first_name' => $request->nama ?? $user->name,
            'email' => $request->email ?? $user->email,
            'phone' => $request->phone ?? ($user->phone), 
            'address' => $request->alamat ?? ($user->address), 
        ];

        // Prepare item details for Midtrans from order items
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id' => $item->product->id, // Use product ID
                'price' => (int) $item->price, // Ensure integer
                'quantity' => $item->quantity,
                'name' => $item->product->title,
            ];
        }

        // Add service fee if applicable (example, adjust as per your logic)
        // $serviceCost = 0; // Example service cost
        // if ($serviceCost > 0) {
        //     $itemDetails[] = [
        //         'id' => 'SERVICE_FEE',
        //         'price' => $serviceCost,
        //         'quantity' => 1,
        //         'name' => 'Biaya Layanan',
        //     ];
        //     $transactionDetails['gross_amount'] += $serviceCost;
        // }


        // Create the Payment record in your database
        $payment = Payment::create([
            'user_id' => $user ? $user->id : null,
            'order_id' => $order->id,
            'id_customer' => $user ? $user->id : null,
            'id_order' => $order->slug, // Use the order slug as the reference ID
            'transaction_details' => json_encode($transactionDetails),
            'customer_details' => json_encode($customerDetails),
            'product_details' => json_encode($itemDetails), // Store item details as product_details
            'status' => '0', // Initial status: Pending
        ]);

        $referrer = null;

        if (Auth::check() && Auth::user()->referrer)
            $referrer = Auth::user()->referrer;
        else if($request->reg != null)
            $referrer = User::where('username',$request->reg)->first();

        $harga = $order->total_price;

        if ($referrer) {
            // generate comission transaction
            $referral_transaction = ReferralService::generateReferralCommission(
                $referrer, // refferer
                $request->user(), // current user refffered
                $harga,
                $payment
            );

            // pengerugnan harga dengn jumlah dari potongan dari referral
            $harga -= $referral_transaction->amount;   
        }
        
        $order_first_item = $order->items->first();

        TransactionService::generateTransactionCheckout(
            $request->user(),
            $order_first_item->product->creator,
            $harga,
            $payment
        );

        try {
            // Get Snap Token from MidtransService
            $snapToken = MidtransService::generateSnapToken([
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
            ]);

            $payment->snap_token = $snapToken;
            $payment->save();

            return $payment;

        } catch (\Exception $e) {
            // Log the error and re-throw or handle as needed
            Log::error("PaymentService: Failed to generate Snap Token for order {$order->id}. Error: " . $e->getMessage());
            // Optionally, delete the created payment record if token generation failed
            $payment->delete();
            throw new \Exception("Failed to initiate payment: " . $e->getMessage());
        }
    }
}