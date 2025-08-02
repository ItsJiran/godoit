<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // For database transactions
use Illuminate\Support\Facades\Cache;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAcquisition; // The model we just created/updated

use App\Enums\Order\OrderStatus; // Assuming you have this Enum
use App\Enums\Acquisition\AcquisitionStatus; // Assuming you have this Enum
use App\Services\Payment\PaymentService;
use App\Services\Order\OrderService;

use Carbon\Carbon;

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
            $request->validate([
                'nama' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'alamat' => 'required',
                'umur' => 'required',
            ]);    
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
        if (Auth::check() && UserAcquisition::userHasActiveProductAcquisition($user->id, $product->id)) {
            return back()->with('error', 'You already have an active acquisition for this product.');
        }

        // 3. Check if the user already has an active (pending) order containing this product
        if (Auth::check() && OrderService::hasPendingOrderForProduct($user->id, $product->id)  ) { 
            return back()->with('error', 'You already have a pending order for this product. Please complete or cancel it first.');            
        }


        // Check if the productable is ProductRegular and its timestamp has exceeded the current time
        if ($product && $product->productable_type === 'App\\Models\\ProductRegular') {
            $productRegular = $product->productable; // Mengakses model ProductRegular
            if ($productRegular && $productRegular->timestamp) {
                // Menggunakan Carbon untuk membandingkan timestamp
                if (Carbon::parse($productRegular->timestamp)->isPast()) {
                    return back()->with('error', 'Event kegiatan sudah melewati batas pelaksanaan.');
                }
            }
        }

        // check if time stamp exceed

        // If all checks pass, proceed to create the order and order item
        DB::beginTransaction();
        try {
            // Create a new Order
            $order = Order::create([
                'user_id' => $user ? $user->id : null,
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
            
            return redirect()->route('payments.show', ['id' => $payment->id])->with('success','Berhasil Checkout, silahkan lakukan pembayaran!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error during product checkout for user " . ($user ? $user->id : 'Guest' . $request->email) . ", product {$productId}: " . $e->getMessage());
            return back()->with('error', 'An error occurred during checkout. Please try again.');
        }
    }
}