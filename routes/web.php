<?php

use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\TableController as AdminTableController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\CustomerMenuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MidtransController;
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

// Public landing & customer menu (isolated per device/browser guest session)
Route::middleware('guest.session')->group(function () {
    Route::get('/', [CustomerMenuController::class, 'landing'])->name('landing');
    Route::get('/meja/{token}', [CustomerMenuController::class, 'scanQr'])->name('customer.qr.scan');
    Route::post('/api/qr/resolve', [CustomerMenuController::class, 'resolveQrScan'])->name('customer.qr.resolve');
    Route::get('/menu', [CustomerMenuController::class, 'index'])->name('customer.menu');
    Route::get('/menu/{menu}', [CustomerMenuController::class, 'show'])
        ->whereNumber('menu')
        ->name('customer.menu.show');
    Route::post('/checkout', [CustomerMenuController::class, 'checkout'])->name('customer.checkout');

    // Customer Cart & QR Order Status (qr-ordering)
    Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
    Route::get('/keranjang', [OrderController::class, 'cart'])->name('cart.keranjang');
    Route::post('/keranjang/update', [OrderController::class, 'updateCart'])->name('cart.update');
    Route::post('/pesanan', [OrderController::class, 'store'])->name('pesanan.store');
    Route::get('/pesanan/{order_code}', [OrderController::class, 'status'])->name('orders.status');
    Route::get('/api/pesanan/{order_code}/status', [OrderController::class, 'checkStatus'])->name('orders.check-status');
    Route::get('/riwayat-pesanan', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/riwayat', [OrderController::class, 'history'])->name('riwayat');
    Route::get('/pesan-qr', [OrderController::class, 'customerQr'])->name('customer.qr');

    // Midtrans Payment Gateway (customer-scoped)
    Route::post('/api/midtrans/orders/{order_code}/token', [MidtransController::class, 'token'])->name('midtrans.token');
    Route::post('/api/midtrans/orders/{order_code}/verify', [MidtransController::class, 'verify'])->name('midtrans.verify');
});

Route::get('/menu/download', [CustomerMenuController::class, 'downloadMenu'])->name('customer.menu.download');
Route::post('/pesanan/{order_code}/update-status', [OrderController::class, 'updateStatus'])
    ->middleware(['auth', 'role:kasir,admin'])
    ->name('orders.update-status');

// Midtrans Payment Gateway Webhook (authenticated by SHA-512 signature)
Route::post('/api/midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');

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
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
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

    // Meja & QR Code (A7)
    Route::get('/meja', [AdminTableController::class, 'index'])->name('tables.index');
    Route::post('/meja', [AdminTableController::class, 'store'])->name('tables.store');
    Route::post('/meja/{table}/toggle', [AdminTableController::class, 'toggleStatus'])->name('tables.toggle');
    Route::get('/meja/{table}/unduh', [AdminTableController::class, 'downloadQr'])->name('tables.download-qr');
    Route::get('/meja/{table}/cetak', [AdminTableController::class, 'print'])->name('tables.print');
});

// Kasir routes (can be accessed by kasir and admin)
Route::middleware(['auth', 'role:kasir,admin'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/riwayat', [OrderController::class, 'history'])->name('riwayat');
    Route::get('/sinkron', [SyncController::class, 'index'])->name('sinkron');
});

// Shortcut route to kasir tablet
Route::get('/pos', [OrderController::class, 'index'])
    ->middleware(['auth', 'role:kasir,admin'])
    ->name('kasir.pos');

// Orders API & Actions
Route::prefix('orders')->name('orders.')->group(function () {
    Route::middleware('guest.session')->group(function () {
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });

    Route::middleware(['auth', 'role:kasir,admin'])->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('list');
        Route::post('/{order}/process', [OrderController::class, 'process'])->name('process');
        Route::post('/{order}/complete', [OrderController::class, 'complete'])->name('complete');
        Route::post('/{order}/reject', [OrderController::class, 'reject'])->name('reject');
        Route::post('/{order}/pay', [OrderController::class, 'pay'])->name('pay');
    });
});

// Sinkron top-level route
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
Route::middleware(['auth', 'role:pelanggan', 'guest.session'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/', [OrderController::class, 'history'])->name('beranda');
    Route::view('/akun', 'pelanggan.akun')->name('akun');
});

Route::get('/api/menu', [MenuController::class, 'index'])->name('api.menu');
