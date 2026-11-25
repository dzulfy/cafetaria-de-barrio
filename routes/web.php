<?php

use App\Http\Controllers\BahanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BahanController::class, 'index'])->name('home');
Route::redirect('/stok-bahan', '/bahan');
Route::redirect('/stok', '/bahan');

Route::prefix('bahan')->name('bahan.')->group(function () {
    Route::get('/', [BahanController::class, 'index'])->name('index');
    Route::post('/', [BahanController::class, 'store'])->name('store');
    Route::post('/mutasi', [BahanController::class, 'mutasi'])->name('mutasi');
    Route::put('/{bahan}', [BahanController::class, 'update'])->name('update');
    Route::delete('/{bahan}', [BahanController::class, 'destroy'])->name('destroy');
});
