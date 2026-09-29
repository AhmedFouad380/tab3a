<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BranchController;
use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\KioskController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PricingController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Taba3a RESTful API Routes (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // 1. Authentication & Onboarding (Public)
    Route::prefix('auth')->group(function () {
        Route::post('/send-otp', [AuthController::class, 'sendOtp']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    });

    // 2. Home, CMS & General Settings (Public / Optional Auth)
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/settings', [HomeController::class, 'getSettings']);
    Route::get('/pages', [HomeController::class, 'getPages']); // Returns list of all active pages, or single by ?slug=...
    Route::get('/faqs', [HomeController::class, 'getFaqs']);
    Route::post('/contact-us', [HomeController::class, 'submitContact']);

    // 3. Branches, Pricing & Options (Public)
    Route::get('/branches', [BranchController::class, 'index']); // Supports ?latitude=...&longitude=... for nearest sorting
    Route::get('/branches/{id}', [BranchController::class, 'show']);
    Route::get('/pricing-rules', [PricingController::class, 'index']);

    // 4. File Processing & Price Calculator
    Route::post('/files/upload-and-inspect', [FileController::class, 'uploadAndInspect']);
    Route::post('/files/calculate-price', [FileController::class, 'calculatePrice']);

    // 5. Kiosk Verification & Pre-Print Health Check
    Route::post('/kiosks/verify-machine', [KioskController::class, 'verifyMachine']);

    // ==========================================
    // Protected Routes (Require Sanctum Auth)
    // ==========================================
    Route::middleware('auth:sanctum')->group(function () {

        // Profile & Auth
        Route::prefix('auth')->group(function () {
            Route::post('/complete-profile', [AuthController::class, 'completeProfile']);
            Route::get('/profile', [AuthController::class, 'getProfile']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });

        // Orders & Checkout
        Route::prefix('orders')->group(function () {
            Route::post('/self-print/checkout', [OrderController::class, 'createSelfPrintOrder']);
            Route::post('/pre-order/checkout', [OrderController::class, 'createPreOrder']);
            Route::get('/', [OrderController::class, 'myOrders']);
            Route::get('/{id}', [OrderController::class, 'show']);
            Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
        });

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        });

        // Wallet
        Route::get('/wallet/transactions', [WalletController::class, 'index']);
    });
});
