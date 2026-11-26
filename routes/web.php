<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\TableController as AdminTableController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Customer routes (Pelanggan)
Route::get('/', [OrderController::class, 'cart'])->name('cart');
Route::get('/keranjang', [OrderController::class, 'cart'])->name('cart.keranjang');
Route::post('/keranjang/update', [OrderController::class, 'updateCart'])->name('cart.update');

Route::post('/pesanan', [OrderController::class, 'store'])->name('orders.store');
Route::get('/pesanan/{order_code}', [OrderController::class, 'status'])->name('orders.status');
Route::get('/api/pesanan/{order_code}/status', [OrderController::class, 'checkStatus'])->name('orders.check-status');
Route::post('/pesanan/{order_code}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
Route::get('/riwayat', [OrderController::class, 'history'])->name('orders.history');

// Admin routes (A7 Meja & QR Code)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/meja', [AdminTableController::class, 'index'])->name('tables.index');
    Route::post('/meja', [AdminTableController::class, 'store'])->name('tables.store');
    Route::post('/meja/{table}/toggle', [AdminTableController::class, 'toggleStatus'])->name('tables.toggle');
    Route::get('/meja/{table}/unduh', [AdminTableController::class, 'downloadQr'])->name('tables.download-qr');
    Route::get('/meja/{table}/cetak', [AdminTableController::class, 'print'])->name('tables.print');
});
