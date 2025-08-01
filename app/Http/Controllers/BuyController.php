<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\Order;
use App\Mail\CustomMail;
use App\Mail\PaymentSuccessAdminNotification;
use App\Mail\PaymentSuccessUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Transaction;
use Midtrans\CoreApi;
use Illuminate\Support\Facades\DB; // Import DB facade
use App\Services\Referral\ReferralService;
use App\Services\Account\TransactionService;
use App\Services\Account\TransactionProcessor;
use App\Services\Order\OrderProcessor;

use App\Enums\Account\AccountTransactionStatus;
use App\Enums\Order\OrderStatus;


// CREATED BY RIO ILHAM HADI (WWW.BLANTERMEDIA.COM)
class BuyController 
{
    // ALUR 1: PEMBAYARAN MIDTRANS (DISABLED)
    public function createPayment(Request $request)
    {
        // Validasi input dari form checkout
        $request->validate([
            'nama' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'alamat' => 'required',
            'umur' => 'required',
        ]);
        // Calculate Price
        $finalPrices = 10000;
        $grossPrices = 0;
        // Detail pembayaran (misalnya dari form atau produk yang dibeli)
        $order_id = uniqid(); // Buat ID pesanan unik
        $transactionDetails = [
            'order_id' => $order_id,
            'gross_amount' => $grossPrices,
        ];
        $customerDetails = [
            'first_name' => $request->nama,
            'email' => $request->email,
            'phone' => $request->phone,
        ];
        // Siapkan array untuk menyimpan detail produk
        //$productDetails = [];
        $totalPrice = 0;
        // Hitung harga produk
        $hargaProduk = 10000;
        $diskon = 0;
        $hargaSetelahDiskon = 10000;
        // Ambil quantity dari array $quantityData
        $quantity = 1;
        // Tambahkan detail produk ke array
        $productDetails[] = [
            'id' => 'PRODUCT-PELATIHAN',
            'price' => $hargaSetelahDiskon,
            'quantity' => 1,
            'name' => 'NAPAK TILAS',
        ];
        // Tambahkan biaya tambahan
        $serviceCost = 0;
        $productDetails[] = [
            'id' => 'SERVICE_FEE',
            'price' => $serviceCost,
            'quantity' => 1,
            'name' => 'Biaya Layanan',
        ];
        // Simpan transaksi di database
        $payment = Payment::create([
            'user_id' => ($request->user() ? $request->user()->id : null),
            'id_customer' => ($request->user() ? $request->user()->id : null),
            'id_order' => $order_id,
            'transaction_details' => json_encode($transactionDetails),
            'customer_details' => json_encode($customerDetails),
            'product_details' => json_encode($productDetails),
            'status' => '0', // 0 is Pending
        ]);
        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION'); // Ubah ke true jika di production
        Config::$isSanitized = true;
        Config::$is3ds = true;
        // Buat Snap Token
        $params = [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails,
            'item_details' => $productDetails,
        ];
        // pembuatan komisi pending 
        try {
            $snapToken = Snap::getSnapToken($params);
            $payment->snap_token = $snapToken;
            $payment->save();
            $referrer = null;
            if($request->reg != null)
                $referrer = User::where('username',$request->reg)->first();
            if($referrer == null && $request->user())
                $referrer = $request->user()->referrer;
            if($referrer == null)
                $referrer = User::where('role','admin')->first();
            // generate comission transaction
            $referral_transaction = ReferralService::generateReferralCommission(
                $referrer, // refferer
                $request->user(), // current user refffered
                $hargaSetelahDiskon,
                $payment
            );
            // pengerugnan harga dengn jumlah dari potongan dari referral
            $hargaSetelahDiskon -= $referral_transaction->amount;                
            TransactionService::generateTransactionCheckout(
                $request->user(),
                $hargaSetelahDiskon,
                $payment
            );
            return redirect()->route('payments.show', ['id' => $payment->id])->with('success','Berhasil Checkout, silahkan lakukan pembayaran!');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // ALUR 2: HALAMAN PEMBAYARAN
    public function showPayment($id)
    {
        $payment = Payment::findOrFail($id);
        // Pastikan casting manual jika ternyata masih string
        if (is_string($payment->product_details)) {
            $payment->product_details = json_decode($payment->product_details, true);
        }
        if (is_string($payment->transaction_details)) {
            $payment->transaction_details = json_decode($payment->transaction_details, true);
        }
        if (is_string($payment->customer_details)) {
            $payment->customer_details = json_decode($payment->customer_details, true);
        }
        return view('payments.payment', ['payment' => $payment]);
    }
    
    // ALUR 3: UPDATE DATA PAYMENT DAN CHECKOUT
    public function updatePaymentStatus(Request $request)
    {
        // Validasi data yang masuk
        $request->validate([
            'status' => 'required|string',
            'order_id' => 'required|string',
        ]);
        DB::beginTransaction(); // Start database transaction
        try {
            // Cari Payment berdasarkan order_id
            $payment = Payment::where('id_order', $request->order_id)->first();
            if (!$payment) {
                DB::rollBack(); // Rollback if payment not found
                return response()->json(['error' => 'Payment not found'], 404);
            }
            // Update status berdasarkan status yang diterima dari JavaScript
            if ($request->status == 'success') {              
                $payment->status = '1'; 
                // Tambahkan parsing JSON
                $customer = json_decode($payment->customer_details);
                $products = json_decode($payment->product_details);
                // Kirim email to Pembeli
                Mail::to($customer->email)->send(new PaymentSuccessUserNotification($payment, $customer, $products));
                // Kirim email to Pembeli
                Mail::to("necromancer080@gmail.com")->send(new PaymentSuccessAdminNotification($payment, $customer, $products));
                // Sukses
                TransactionService::processSourceableTransactions( $payment, AccountTransactionStatus::COMPLETED );
                if($payment->order){ OrderProcessor::completeOrder( $payment->order ); }
            } elseif ($request->status == 'pending') {
                $payment->status = '0'; // Pending
            } elseif ($request->status == 'error') {
                $payment->status = '2'; // Error
                TransactionService::processSourceableTransactions( $payment, AccountTransactionStatus::FAILED );
            }
            // Simpan perubahan ke database
            $payment->save();
            DB::commit(); // Commit the transaction if all operations are successful
            // Return response sebagai indikasi bahwa update berhasil
            return response()->json(['status' => 'Payment status updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback in case of any exception
            // Log the error for debugging
            \Log::error('Error updating payment status: ' . $e->getMessage(), [
                'order_id' => $request->order_id,
                'status' => $request->status,
                'exception' => $e
            ]);
            return response()->json(['error' => 'Failed to update payment status', 'message' => $e->getMessage()], 500);
        }
    }


    // ALUR 4: Handle Midtrans notifications (DISABLED)
    public function notificationHandler(Request $request)
    {
        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
        // Tangkap notifikasi dari Midtrans
        $notification = new Notification();
        // Ambil informasi dari notifikasi
        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;
        $paymentType = $notification->payment_type;
        $fraudStatus = $notification->fraud_status;
        // Cari pembayaran berdasarkan order_id
        $payment = Payment::where('id_order', $orderId)->first();
        if (!$payment) {
            Log::error('Payment not found for order ID: ' . $orderId);
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }
        // Cek status transaksi
        if ($transactionStatus == 'settlement') {
            $payment->status = '1'; // Pembayaran berhasil
            TransactionService::processSourceableTransactions(
                $payment,
                AccountTransactionStatus::COMPLETED
            );
        } elseif ($transactionStatus == 'pending') {
            $payment->status = '0'; // Pembayaran pending
        } elseif ($transactionStatus == 'deny') {
            $payment->status = '3'; // Pembayaran ditolak
            TransactionService::processSourceableTransactions(
                $payment,
                AccountTransactionStatus::FAILED
            );
        } elseif ($transactionStatus == 'expire') {
            $payment->status = '4'; // Pembayaran kadaluwarsa
            TransactionService::processSourceableTransactions(
                $payment,
                AccountTransactionStatus::FAILED
            );
        } elseif ($transactionStatus == 'cancel') {
            $payment->status = '5'; // Pembayaran dibatalkan
            TransactionService::processSourceableTransactions(
                $payment,
                AccountTransactionStatus::FAILED
            );
        }
        // Simpan perubahan status ke database
        $payment->save();
        // Berikan response ke Midtrans
        return response()->json(['status' => 'success']);
    }

    // MANUAL UPDATE PAYMENT (DISABLED)
    public function manualUpdate(Request $request, $payment_id, $status)
    {
        $payment = Payment::where('id', $payment_id)->first();
        // Cek status transaksi
        if ($status == 'settlement') {
            $payment->status = '1'; // Pembayaran berhasil
            TransactionService::processSourceableTransactions(
                $payment,
                AccountTransactionStatus::COMPLETED
            );
        } elseif ($status == 'pending') {
            $payment->status = '0'; // Pembayaran pending
        } elseif ($status == 'deny') {
            $payment->status = '3'; // Pembayaran ditolak
            TransactionService::processSourceableTransactions(
                $payment,
                AccountTransactionStatus::FAILED
            );
        } elseif ($status == 'expire') {
            $payment->status = '4'; // Pembayaran kadaluwarsa
            TransactionService::processSourceableTransactions(
                $payment,
                AccountTransactionStatus::FAILED
            );
        } elseif ($status == 'cancel') {
            $payment->status = '5'; // Pembayaran dibatalkan
            TransactionService::processSourceableTransactions(
                $payment,
                AccountTransactionStatus::FAILED
            );
        }
        $payment->save();
        return response()->json(['status' => 'success']);
    }

    // ALUR 5: Display status of payment
    public function paymentStatus($id)
    {
        $payment = Payment::findOrFail($id);
        // Pastikan casting manual jika ternyata masih string
        if (is_string($payment->product_details)) {
            $payment->product_details = json_decode($payment->product_details, true);
        }
        if (is_string($payment->transaction_details)) {
            $payment->transaction_details = json_decode($payment->transaction_details, true);
        }
        if (is_string($payment->customer_details)) {
            $payment->customer_details = json_decode($payment->customer_details, true);
        }
        return view('payments.payment-status', ['payment' => $payment]);
    }

    // ALUR 6: JIKA TIDAK REDIRECT MAKA UPDATE SECARA MANUAL
    public function checkPaymentStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);
        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
        try {
            $status = \Midtrans\Transaction::status($request->order_id);
            if ($status) {
                $transaction_status = $status->transaction_status;
                $order_id = $status->order_id;
                $payment = Payment::where('id_order', $order_id)->first();
                if (!$payment) {
                    return response()->json(['error' => 'Payment not found in database'], 404);
                }
                // Perbarui status di database berdasarkan status dari Midtrans
                switch ($transaction_status) {
                    case 'capture':
                    case 'settlement':
                        $payment->status = '1'; // Sukses
                        break;
                    case 'pending':
                        $payment->status = '0'; // Pending
                        break;
                    case 'deny':
                    case 'expire':
                    case 'cancel':
                        $payment->status = '2'; // Gagal
                        break;
                    default:
                        break;
                }
                $payment->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment status updated successfully',
                    'midtrans_status' => $transaction_status,
                    'data' => $payment
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error checking payment status from Midtrans: ' . $e->getMessage(), [
                'order_id' => $request->order_id,
                'exception' => $e
            ]);
            return response()->json(['error' => 'Failed to check payment status', 'message' => $e->getMessage()], 500);
        }
    }

}
