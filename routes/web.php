<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\BuyController;
use App\Http\Controllers\Checkout\CheckoutController;

use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'home'])->name('welcome.index');

Route::middleware('auth')->group(function () {
    // ALUR PEMBAYARAN
    Route::post('/payments/create', [BuyController::class, 'createPayment'])->name('payments.create');
    Route::get('/payments/{id}', [BuyController::class, 'showPayment'])->name('payments.show');
    Route::post('/payment/notification', [BuyController::class, 'notificationHandler'])->name('payment.notification');
    Route::post('/transaction/pay', [BuyController::class, 'updatePaymentStatus'])->name('transaction.pay');
    Route::get('/payment/status/{id}', [BuyController::class, 'paymentStatus'])->name('payment.status');
    Route::get('/update/payment/status/{id}/{status}', [BuyController::class, 'manualUpdate']);

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // PROFILE DATA
    Route::get('/profile', [ProfileController::class, 'myprofile'])->name('profile.myprofile');
    Route::get('/edit-profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/edit-profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';