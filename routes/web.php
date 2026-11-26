<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\CustomerMenuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Warkop De Barrio
|--------------------------------------------------------------------------
*/

// Public landing & customer menu
Route::view('/', 'pelanggan.landing')->name('landing');
Route::get('/menu', [CustomerMenuController::class, 'index'])->name('customer.menu');
Route::post('/checkout', [CustomerMenuController::class, 'checkout'])->name('customer.checkout');

// Guest routes (not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/akun/kata-sandi', [ProfileController::class, 'edit'])->name('password.edit');
    Route::put('/akun/kata-sandi', [ProfileController::class, 'update'])->name('password.update');
});

// Admin Dashboard & Inventory / Pengeluaran aliases
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::redirect('/stok-bahan', '/bahan');
Route::redirect('/stok', '/bahan');

// Bahan & Mutasi Stok
Route::prefix('bahan')->name('bahan.')->group(function () {
    Route::get('/', [BahanController::class, 'index'])->name('index');
    Route::post('/', [BahanController::class, 'store'])->name('store');
    Route::post('/mutasi', [BahanController::class, 'mutasi'])->name('mutasi');
    Route::put('/{bahan}', [BahanController::class, 'update'])->name('update');
    Route::delete('/{bahan}', [BahanController::class, 'destroy'])->name('destroy');
});

// Pengeluaran Operasional
Route::prefix('pengeluaran')->name('pengeluaran.')->group(function () {
    Route::get('/', [PengeluaranController::class, 'index'])->name('index');
    Route::post('/', [PengeluaranController::class, 'store'])->name('store');
    Route::put('/{pengeluaran}', [PengeluaranController::class, 'update'])->name('update');
    Route::delete('/{pengeluaran}', [PengeluaranController::class, 'destroy'])->name('destroy');
});

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::resource('users', UserController::class)->except('show');

    // Menu Management in Admin
    Route::get('/menu', [AdminMenuController::class, 'index'])->name('menus.index');
    Route::post('/menu', [AdminMenuController::class, 'store'])->name('menus.store');
    Route::put('/menu/{menu}', [AdminMenuController::class, 'update'])->name('menus.update');
    Route::delete('/menu/{menu}', [AdminMenuController::class, 'destroy'])->name('menus.destroy');
    Route::patch('/menu/{menu}/toggle-status', [AdminMenuController::class, 'toggleStatus'])->name('menus.toggle');

    // Inventory & Pengeluaran inside Admin
    Route::get('/inventory', [BahanController::class, 'index'])->name('inventory.index');
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
});

// Kasir routes (can be accessed by kasir and admin)
Route::prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/riwayat', [OrderController::class, 'history'])->name('riwayat');
    Route::get('/sinkron', [SyncController::class, 'index'])->name('sinkron');
});

// Shortcut route to kasir tablet
Route::get('/pos', [OrderController::class, 'index'])->name('kasir.pos');

// Orders API & Actions
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('list');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::post('/{order}/process', [OrderController::class, 'process'])->name('process');
    Route::post('/{order}/reject', [OrderController::class, 'reject'])->name('reject');
    Route::post('/{order}/pay', [OrderController::class, 'pay'])->name('pay');
});

// Riwayat & Sinkron top-level routes
Route::get('/riwayat', [OrderController::class, 'history'])->name('riwayat');
Route::get('/sinkron', [SyncController::class, 'index'])->name('sinkron');

// Offline Sync API
Route::prefix('sync')->name('sync.')->group(function () {
    Route::get('/status', [SyncController::class, 'index'])->name('status');
    Route::post('/now', [SyncController::class, 'syncNow'])->name('now');
    Route::post('/retry-failed', [SyncController::class, 'retryFailed'])->name('retry');
    Route::post('/toggle-connection', [SyncController::class, 'toggleConnection'])->name('toggle');
    Route::post('/add-offline', [SyncController::class, 'addOfflineItem'])->name('add');
});

// Pelanggan routes
Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::view('/', 'pelanggan.beranda')->name('beranda');
    Route::view('/akun', 'pelanggan.akun')->name('akun');
});

// Customer QR Ordering Simulation
Route::get('/pesan-qr', [OrderController::class, 'customerQr'])->name('customer.qr');
Route::get('/api/menu', [MenuController::class, 'index'])->name('api.menu');
