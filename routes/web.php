<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Warkop De Barrio
|--------------------------------------------------------------------------
*/

// Kasir Tablet View (Default Screen from Figma: K3 Pesanan Masuk QR)
Route::get('/', [OrderController::class, 'index'])->name('kasir.index');

// Orders API & Actions
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('list');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::post('/{order}/process', [OrderController::class, 'process'])->name('process');
    Route::post('/{order}/reject', [OrderController::class, 'reject'])->name('reject');
    Route::post('/{order}/pay', [OrderController::class, 'pay'])->name('pay');
});

// Kasir Riwayat Transaksi (K5)
Route::get('/riwayat', [OrderController::class, 'history'])->name('kasir.riwayat');

// Menu API
Route::get('/api/menu', [MenuController::class, 'index'])->name('api.menu');

// Pelanggan Mobile QR Ordering Simulation
Route::get('/pesan-qr', [OrderController::class, 'customerQr'])->name('customer.qr');
