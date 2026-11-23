<?php

use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\CustomerMenuController;
use Illuminate\Support\Facades\Route;

// Customer Menu View (Tampilan Pelanggan - Image 2)
Route::get('/', [CustomerMenuController::class, 'index'])->name('customer.menu');
Route::post('/checkout', [CustomerMenuController::class, 'checkout'])->name('customer.checkout');

// Admin Panel View (Panel Admin Menu & Kategori - Image 1)
Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::redirect('/', '/admin/menu');
    Route::get('/menu', [AdminMenuController::class, 'index'])->name('menus.index');
    Route::post('/menu', [AdminMenuController::class, 'store'])->name('menus.store');
    Route::put('/menu/{menu}', [AdminMenuController::class, 'update'])->name('menus.update');
    Route::delete('/menu/{menu}', [AdminMenuController::class, 'destroy'])->name('menus.destroy');
    Route::patch('/menu/{menu}/toggle-status', [AdminMenuController::class, 'toggleStatus'])->name('menus.toggle');
});
