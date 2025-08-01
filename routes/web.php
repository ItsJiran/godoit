<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BuyController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Product\PremiumMembershipController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Checkout\CheckoutController;
use Illuminate\Support\Facades\Route;

// HOME
Route::get('/', [DashboardController::class, 'home'])->name('welcome.index');
Route::get('/page/napak_tilas', [DashboardController::class, 'napaktilas'])->name('welcome.index');

// PRODUCT
Route::get('/product/{product_id}', [DashboardController::class, 'product'])->name('welcome_product.index');
Route::get('/demo/checkout', [CheckoutController::class, 'showCheckoutForm'])->name('checkout.form');
Route::get('/product/view/{id}', [ProductController::class, 'viewProduct'])->name('product.view');
Route::post('/product/checkout', [CheckoutController::class, 'checkoutProduct'])->name('product.checkout');

// ALUR PEMBAYARAN
Route::post('/payments/create', [BuyController::class, 'createPayment'])->name('payments.create');
Route::get('/payments/{id}', [BuyController::class, 'showPayment'])->name('payments.show');
Route::post('/payment/notification', [BuyController::class, 'notificationHandler'])->name('payment.notification');
Route::post('/transaction/pay', [BuyController::class, 'updatePaymentStatus'])->name('transaction.pay');
Route::get('/payment/status/{id}', [BuyController::class, 'paymentStatus'])->name('payment.status');
//Route::get('/update/payment/status/{id}/{status}', [BuyController::class, 'manualUpdate']);
Route::post('/payment/check-status', [BuyController::class, 'checkPaymentStatus'])->name('payment.check-status');

Route::middleware('auth')->group(function () {
    // DASHBOARD (ADMIN)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // admin settings
    Route::get('/admin/setting', [DashboardController::class, 'editSetting'])->name('editSetting');
    Route::put('/admin/setting', [DashboardController::class, 'updateSetting'])->name('updateSetting');
    
    // section
    Route::get('/admin/page/napak', [PageController::class, 'indexPageSectionNapak'])->name('page.napak');
    Route::get('/admin/page/homepage', [PageController::class, 'indexPageSectionHome'])->name('page.homepage');
    Route::get('/admin/page/section/create/{landing_type}/{type}', [PageController::class, 'createSection'])->name('createSection');
    Route::post('/admin/page/section/store', [PageController::class, 'storeSection'])->name('storeSection');
    Route::get('/admin/page/section/{id}/edit', [PageController::class, 'editSection'])->name('editSection');
    Route::put('/admin/page/section/{id}', [ProductController::class, 'updateSection'])->name('updateSection');
    Route::delete('/admin/page/section/{id}', [PageController::class, 'deleteSection'])->name('deleteSection');

    // PRODUCT (ADMIN)
    Route::get('/admin/product', [ProductController::class, 'index'])->name('admin.product');
    Route::post('/admin/product', [ProductController::class, 'saveProduct'])->name('saveProduct');
    Route::get('/admin/product/{id}/edit', [ProductController::class, 'editProduct'])->name('editProduct');
    Route::put('/admin/product/{id}', [ProductController::class, 'updateProduct'])->name('updateProduct');
    Route::delete('/admin/product/{id}', [ProductController::class, 'deleteProduct'])->name('deleteProduct');

    // MARKETING KIT (ADMIN)
    Route::get('/admin/marketing-kit', [DashboardController::class, 'marketing_kit'])->name('marketingkit');
    Route::post('/admin/marketing-kit', [DashboardController::class, 'simpankit'])->name('simpankit');
    Route::get('/admin/marketing-kit/{id}/edit', [DashboardController::class, 'editkit'])->name('editkit');
    Route::put('/admin/marketing-kit/{id}', [DashboardController::class, 'updatekit'])->name('updatekit');
    Route::delete('/admin/marketing-kit/{id}', [DashboardController::class, 'hapuskit'])->name('hapuskit');

    // USERS (ADMIN)
    Route::get('/admin/all-users', [DashboardController::class, 'allusers'])->name('allusers');
    Route::put('/admin/blokir-user/{id}', [DashboardController::class, 'blokiruser'])->name('blokiruser');
    Route::put('/admin/unblock-user/{id}', [DashboardController::class, 'unblokiruser'])->name('unblokiruser');
    Route::get('/admin/edit-user/{id}', [DashboardController::class, 'edituser'])->name('edituser');
    Route::put('/admin/edit-user/{id}', [DashboardController::class, 'adminedituser'])->name('adminedituser');
    Route::get('/admin/inbox', [DashboardController::class, 'inbox'])->name('admin.inbox');

    // TRANSACTION (ADMIN)
    Route::get('/admin/transaction', [DashboardController::class, 'admin_transaction'])->name('admin.transaction');
    Route::get('/admin/withdraw', [DashboardController::class, 'admin_withdraw'])->name('admin.withdraw');
    Route::post('/admin/withdraw/update/{id}', [DashboardController::class, 'admin_withdraw_update'])->name('admin.withdraw.update');

    // PAGES (USER) NEED TO LOGIN
    Route::get('/memberarea', [PageController::class, 'memberarea'])->name('page.memberarea');
    Route::get('/transaction', [PageController::class, 'transaction'])->name('page.transaction');

    // WALLET KEUANGAN (USER)
    Route::get('/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
    Route::post('/withdraw', [WalletController::class, 'withdrawStore'])->name('wallet.withdraw.store');
    Route::get('/my-bank', [WalletController::class, 'mybank'])->name('wallet.mybank');
    Route::post('/my-bank/save', [WalletController::class, 'saveBank'])->name('wallet.savebank');

    // CHECKOUT PRODUCT (USER)
    Route::get('/membership/upgrade', [PremiumMembershipController::class, 'index'])->name('membership.upgrade');

    // PROFILE DATA (AUTH)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// PAGES (GUEST or USER) NO LOGIN
Route::get('/marketing-kit', [PageController::class, 'marketing_kit'])->name('page.marketingkit');
Route::get('/contact', [PageController::class, 'contact'])->name('page.contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('page.contact.submit');

require __DIR__.'/auth.php';