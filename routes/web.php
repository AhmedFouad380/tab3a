<?php

use App\Http\Controllers\Admin\OrderFileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/landing', function () {
    $appName = rescue(fn () => SystemSetting::where('key', 'app_name')->first()?->value['ar'] ?? 'منصة طباعة', 'منصة طباعة');
    $supportPhone = rescue(fn () => SystemSetting::where('key', 'support_phone')->first()?->value['ar'] ?? '+966500000000', '+966500000000');
    $branchesCount = rescue(fn () => Branch::count(), 6);
    $ordersCount = rescue(fn () => Order::count(), 1450);
    $kiosksCount = rescue(fn () => KioskMachine::count(), 18);

    return view('welcome', compact('appName', 'supportPhone', 'branchesCount', 'ordersCount', 'kiosksCount'));
})->name('landing');

Route::middleware(['auth:admin'])->prefix('admin/orders')->name('admin.orders.')->group(function () {
    Route::get('/{order}/download', [OrderFileController::class, 'downloadOrder'])->name('download');
    Route::get('/items/{orderItem}/download', [OrderFileController::class, 'downloadItem'])->name('items.download');
    Route::get('/items/{orderItem}/preview', [OrderFileController::class, 'previewItem'])->name('items.preview');
});

// Kiosk Sticker & QR Code Downloads
use App\Http\Controllers\KioskScreenController;
use App\Http\Controllers\KioskStickerController;
use App\Models\Branch;
use App\Models\KioskMachine;
use App\Models\Order;
use App\Models\SystemSetting;

Route::get('/kiosks/{kiosk}/qr-download', [KioskStickerController::class, 'downloadQr'])->name('kiosk.qr.download');
Route::get('/kiosks/{kiosk}/sticker', [KioskStickerController::class, 'printSticker'])->name('kiosk.sticker');

// Kiosk Terminal Screen & Mobile Interactions
Route::get('/kiosks/{machine_code}/screen', [KioskScreenController::class, 'show'])->name('kiosk.screen');
Route::get('/kiosk-connect/{token}', [KioskScreenController::class, 'connect'])->name('kiosk.mobile.connect');
Route::post('/kiosk-session/{token}/upload', [KioskScreenController::class, 'mobileUpload'])->name('kiosk.mobile.upload');
Route::post('/kiosk-session/{token}/pay', [KioskScreenController::class, 'mobilePay'])->name('kiosk.mobile.pay');
Route::post('/kiosk/complete-reset/{token}', [KioskScreenController::class, 'completeAndReset'])->name('kiosk.reset');
Route::get('/api/kiosks/{machine_code}/screen-status', [KioskScreenController::class, 'screenStatus'])->name('api.kiosk.screen.status');
