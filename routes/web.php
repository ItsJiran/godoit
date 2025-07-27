<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\BuyController;
use App\Http\Controllers\Checkout\CheckoutController;

use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'home'])->name('welcome.index');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
//Route::get('/admin/schedule', [DashboardController::class, 'schedule'])->name('dashboard.schedule');
//Route::get('/admin/transaction', [DashboardController::class, 'index'])->name('dashboard.index');
//Route::get('/admin/marketing-kit', [DashboardController::class, 'index'])->name('dashboard.index');
//Route::get('/admin/all-users', [DashboardController::class, 'index'])->name('dashboard.index');
//Route::get('/admin/web-settings', [DashboardController::class, 'index'])->name('dashboard.index');


Route::middleware('auth')->group(function () {
    // ALUR PEMBAYARAN
    Route::post('/payments/create', [BuyController::class, 'createPayment'])->name('payments.create');
    Route::get('/payments/{id}', [BuyController::class, 'showPayment'])->name('payments.show');
    Route::post('/payment/notification', [BuyController::class, 'notificationHandler'])->name('payment.notification');
    Route::post('/transaction/pay', [BuyController::class, 'updatePaymentStatus'])->name('transaction.pay');
    Route::get('/payment/status/{id}', [BuyController::class, 'paymentStatus'])->name('payment.status');

    Route::get('/demo/checkout', [CheckoutController::class, 'showCheckoutForm'])->name('checkout.form');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';