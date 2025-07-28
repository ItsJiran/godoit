<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // For database transactions

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAcquisition; // The model we just created/updated

use App\Enums\Order\OrderStatus; // Assuming you have this Enum
use App\Enums\Acquisition\AcquisitionStatus; // Assuming you have this Enum
use App\Services\Payment\PaymentService;

class CheckoutController extends Controller
{
    /**
     * Display the checkout form.
     *
     * @return \Illuminate\View\View
     */
    public function showCheckoutForm()
    {
        // This method would typically receive a product ID or cart information
        // to display the relevant product details on the checkout page.
        // For now, it just returns the view.
        return view('dashboard.demo.checkout');
    }

    /**
     * Handles the checkout process for a product.
     *
     * This method performs the following checks:
     * 1. Validates if the product exists.
     * 2. Checks if the user already has an active acquisition for the product.
     * 3. Checks if the user already has an active (pending) order for the product.
     * If all checks pass, it creates a new order and order item.
     *
     * @param Request $request The incoming HTTP request, expected to contain 'product_id'.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function checkoutProduct(Request $request)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to complete your purchase.');
        }

        $user = Auth::user();

        // 1. Validate the incoming product ID
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $productId = $request->input('product_id');
        $product = Product::find($productId);

        // If product somehow not found after validation (shouldn't happen with exists rule, but as a safeguard)
        if (!$product) {
            return back()->with('error', 'Product not found.');
        }

        // 2. Check if the user already has an active acquisition for this product
        if (UserAcquisition::userHasActiveProductAcquisition($user->id, $product->id)) {
            return back()->with('error', 'You already have an active acquisition for this product.');
        }

        // 3. Check if the user already has an active (pending) order containing this product
        $existingActiveOrder = Order::where('user_id', $user->id)
                                    ->where('status', OrderStatus::PENDING->value) // Assuming PENDING is the status for active, unfulfilled orders
                                    ->whereHas('orderItems', function ($query) use ($productId) {
                                        $query->where('product_id', $productId);
                                    })
                                    ->first();

        if ($existingActiveOrder) {
            return back()->with('error', 'You already have a pending order for this product. Please complete or cancel it first.');
        }

        // If all checks pass, proceed to create the order and order item
        DB::beginTransaction();
        try {
            // Create a new Order
            $order = Order::create([
                'user_id' => $user->id ?? null,
                'total_price' => $product->price, // Assuming simple case where order total is just product price
                'status' => OrderStatus::PENDING->value, // Set initial status to PENDING
                // Add other relevant order fields like currency, payment_method, etc.
            ]);

            // Create an OrderItem for the product
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 1, // Assuming one quantity per product for now
                'price' => $product->price,
                'total_price' => $product->price,
            ]);

            $payment = PaymentService::generatePaymentForOrder($order, $request);


            DB::commit();

            // Redirect to a payment gateway or a confirmation page
            // You would typically redirect to a payment page here, or a success message.
            return redirect()->route('payments.show', ['id' => $payment->id])->with('success','Berhasil Checkout, silahkan lakukan pembayaran!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error during product checkout for user {$user->id}, product {$productId}: " . $e->getMessage());
            return back()->with('error', 'An error occurred during checkout. Please try again.');
        }
    }
}