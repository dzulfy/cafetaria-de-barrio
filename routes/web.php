<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SyncController;
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

// K6: Sinkronisasi Offline
Route::get('/sinkron', [SyncController::class, 'index'])->name('kasir.sinkron');
Route::prefix('sync')->name('sync.')->group(function () {
    Route::get('/status', [SyncController::class, 'index'])->name('status');
    Route::post('/now', [SyncController::class, 'syncNow'])->name('now');
    Route::post('/retry-failed', [SyncController::class, 'retryFailed'])->name('retry');
    Route::post('/toggle-connection', [SyncController::class, 'toggleConnection'])->name('toggle');
    Route::post('/add-offline', [SyncController::class, 'addOfflineItem'])->name('add');
});

// Menu API
Route::get('/api/menu', [MenuController::class, 'index'])->name('api.menu');

// Pelanggan Mobile QR Ordering Simulation
Route::get('/pesan-qr', [OrderController::class, 'customerQr'])->name('customer.qr');
