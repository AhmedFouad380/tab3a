<?php

use App\Http\Controllers\Admin\OrderFileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/landing', function () {
    $appName = rescue(fn () => \App\Models\SystemSetting::where('key', 'app_name')->first()?->value['ar'] ?? 'منصة طباعة', 'منصة طباعة');
    $supportPhone = rescue(fn () => \App\Models\SystemSetting::where('key', 'support_phone')->first()?->value['ar'] ?? '+966500000000', '+966500000000');
    $branchesCount = rescue(fn () => \App\Models\Branch::count(), 6);
    $ordersCount = rescue(fn () => \App\Models\Order::count(), 1450);
    $kiosksCount = rescue(fn () => \App\Models\KioskMachine::count(), 18);
    
    return view('welcome', compact('appName', 'supportPhone', 'branchesCount', 'ordersCount', 'kiosksCount'));
})->name('landing');

Route::middleware(['auth:admin'])->prefix('admin/orders')->name('admin.orders.')->group(function () {
    Route::get('/{order}/download', [OrderFileController::class, 'downloadOrder'])->name('download');
    Route::get('/items/{orderItem}/download', [OrderFileController::class, 'downloadItem'])->name('items.download');
    Route::get('/items/{orderItem}/preview', [OrderFileController::class, 'previewItem'])->name('items.preview');
});
