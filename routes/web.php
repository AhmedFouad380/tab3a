<?php

use App\Http\Controllers\Admin\OrderFileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:admin'])->prefix('admin/orders')->name('admin.orders.')->group(function () {
    Route::get('/{order}/download', [OrderFileController::class, 'downloadOrder'])->name('download');
    Route::get('/items/{orderItem}/download', [OrderFileController::class, 'downloadItem'])->name('items.download');
    Route::get('/items/{orderItem}/preview', [OrderFileController::class, 'previewItem'])->name('items.preview');
});
