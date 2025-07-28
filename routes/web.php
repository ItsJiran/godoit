<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BuyController;
use App\Http\Controllers\Product\PremiumMembershipController;
use App\Http\Controllers\Checkout\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'home'])->name('welcome.index');
//Route::get('/admin/schedule', [DashboardController::class, 'schedule'])->name('dashboard.schedule');
//Route::get('/admin/transaction', [DashboardController::class, 'index'])->name('dashboard.index');
//Route::get('/admin/marketing-kit', [DashboardController::class, 'index'])->name('dashboard.index');
//Route::get('/admin/all-users', [DashboardController::class, 'index'])->name('dashboard.index');
//Route::get('/admin/web-settings', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/demo/checkout', [CheckoutController::class, 'showCheckoutForm'])->name('checkout.form');

// ALUR PEMBAYARAN
Route::post('/payments/create', [BuyController::class, 'createPayment'])->name('payments.create');
Route::get('/payments/{id}', [BuyController::class, 'showPayment'])->name('payments.show');
Route::post('/payment/notification', [BuyController::class, 'notificationHandler'])->name('payment.notification');
Route::post('/transaction/pay', [BuyController::class, 'updatePaymentStatus'])->name('transaction.pay');
Route::get('/payment/status/{id}', [BuyController::class, 'paymentStatus'])->name('payment.status');
Route::get('/update/payment/status/{id}/{status}', [BuyController::class, 'manualUpdate']);

Route::middleware('auth')->group(function () {
    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/admin/products', [DashboardController::class, 'products'])->name('products');

    // MARKETING KIT
    Route::get('/admin/marketing-kit', [DashboardController::class, 'marketing_kit'])->name('marketingkit');
    Route::post('/admin/marketing-kit', [DashboardController::class, 'simpankit'])->name('simpankit');
    Route::get('/admin/marketing-kit/{id}/edit', [DashboardController::class, 'editkit'])->name('editkit');
    Route::put('/admin/marketing-kit/{id}', [DashboardController::class, 'updatekit'])->name('updatekit');
    Route::delete('/admin/marketing-kit/{id}', [DashboardController::class, 'hapuskit'])->name('hapuskit');

    // USERS
    Route::get('/admin/all-users', [DashboardController::class, 'allusers'])->name('allusers');
    Route::put('/admin/blokir-user/{id}', [DashboardController::class, 'blokiruser'])->name('blokiruser');
    Route::put('/admin/unblock-user/{id}', [DashboardController::class, 'unblokiruser'])->name('unblokiruser');
    Route::get('/admin/edit-user/{id}', [DashboardController::class, 'edituser'])->name('edituser');
    Route::put('/admin/edit-user/{id}', [DashboardController::class, 'adminedituser'])->name('adminedituser');

    // TRANSACTION
    Route::get('/admin/transaction', [DashboardController::class, 'admin_transaction'])->name('admin.transaction');

    // CHECKOUT PRODUCT
    Route::post('/product/checkout', [CheckoutController::class, 'checkoutProduct'])->name('product.checkout');
    Route::get('/membership/upgrade', [PremiumMembershipController::class, 'index'])->name('membership.upgrade');

    // PROFILE DATA
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';