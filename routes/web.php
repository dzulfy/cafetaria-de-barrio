<?php

use App\Http\Controllers\BahanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengeluaranController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

Route::redirect('/stok-bahan', '/bahan');
Route::redirect('/stok', '/bahan');

Route::prefix('bahan')->name('bahan.')->group(function () {
    Route::get('/', [BahanController::class, 'index'])->name('index');
    Route::post('/', [BahanController::class, 'store'])->name('store');
    Route::post('/mutasi', [BahanController::class, 'mutasi'])->name('mutasi');
    Route::put('/{bahan}', [BahanController::class, 'update'])->name('update');
    Route::delete('/{bahan}', [BahanController::class, 'destroy'])->name('destroy');
});

Route::prefix('pengeluaran')->name('pengeluaran.')->group(function () {
    Route::get('/', [PengeluaranController::class, 'index'])->name('index');
    Route::post('/', [PengeluaranController::class, 'store'])->name('store');
    Route::put('/{pengeluaran}', [PengeluaranController::class, 'update'])->name('update');
    Route::delete('/{pengeluaran}', [PengeluaranController::class, 'destroy'])->name('destroy');
});
