# Modul User Management: Warkop Cafetaria De Barrio

Panduan pengerjaan modul **User Management** (Laravel) untuk proyek UAS Framework Pemrograman Web. Cakupan tugas ini:

1. Halaman **login** untuk 3 pengguna (Admin, Kasir, Pelanggan).
2. Setiap pengguna **masuk ke tampilannya masing-masing** sesuai hak akses.
3. **Kelola pengguna** oleh Admin (tambah, ubah, nonaktifkan, hapus, cari, filter).
4. Logout, registrasi pelanggan, dan ubah kata sandi.

Desain mengacu ke file Figma proyek, dan struktur tabel mengacu ke `Database_Warkop_De_Barrio.md`.

---

## 1. Referensi Desain Figma

File: https://www.figma.com/design/Qp5skwmEmj8FtXO7Fo5N9p

| Kebutuhan | Halaman Figma | Frame | Node ID | Link |
|---|---|---|---|---|
| Login Kasir (tablet/desktop, layout dua panel) | Kasir (Tablet) | K1 Login Kasir | `4:52` | [buka](https://www.figma.com/design/Qp5skwmEmj8FtXO7Fo5N9p?node-id=4-52) |
| Login dan Daftar Pelanggan (mobile) | Pelanggan (Mobile) | 05 Masuk & Daftar | `3:94` | [buka](https://www.figma.com/design/Qp5skwmEmj8FtXO7Fo5N9p?node-id=3-94) |
| Akun Pelanggan (ubah kata sandi, keluar) | Pelanggan (Mobile) | 07 Akun | `3:236` | [buka](https://www.figma.com/design/Qp5skwmEmj8FtXO7Fo5N9p?node-id=3-236) |
| Kelola Pengguna dan matriks hak akses | Admin (Desktop) | A6 Pengguna & Hak Akses | `7:558` | [buka](https://www.figma.com/design/Qp5skwmEmj8FtXO7Fo5N9p?node-id=7-558) |
| Tampilan awal Admin | Admin (Desktop) | A1 Dashboard | `6:216` | [buka](https://www.figma.com/design/Qp5skwmEmj8FtXO7Fo5N9p?node-id=6-216) |
| Tampilan awal Kasir | Kasir (Tablet) | K2 Kasir - Pesanan Baru | `4:265` | [buka](https://www.figma.com/design/Qp5skwmEmj8FtXO7Fo5N9p?node-id=4-265) |
| Tampilan awal Pelanggan | Pelanggan (Mobile) | 06 Riwayat Pesanan | `3:180` | [buka](https://www.figma.com/design/Qp5skwmEmj8FtXO7Fo5N9p?node-id=3-180) |

### Design tokens (dari Figma)

| Token | Nilai | Pemakaian |
|---|---|---|
| `brand-900` | `#1F4D3A` | Hijau tua: tombol utama, sidebar, panel merek |
| `brand-700` | `#2E6B52` | Hijau menengah: item menu aktif sidebar |
| `brand-100` | `#E3EDE7` | Hijau muda: tab, badge Pelanggan |
| `cream` | `#F6EFD8` | Krem: foto placeholder, ikon di atas hijau |
| `cream-50` | `#FBF8EE` | Latar halaman |
| `ink` | `#1B2B24` | Teks utama |
| `muted` | `#66776D` | Teks sekunder |
| `line` | `#E6E0CC` | Garis dan border |
| `warn-bg` / `warn-fg` | `#FCEFD0` / `#8A5A0B` | Badge Kasir, peringatan |
| `danger` / `danger-bg` | `#B3402F` / `#FBE7E3` | Error, tombol hapus |
| `ok-bg` | `#DDEEE3` | Badge sukses |
| Font | **Inter** (400, 500, 600, 700) | Seluruh teks |
| Radius | input/tombol 12 px, kartu 14 px, badge/chip penuh | |
| Lebar | panel merek login 460 px, form 380 px, sidebar admin 240 px, rail kasir 96 px | |

---

## 2. Pengguna dan Hak Akses

### 2.1 Akun demo (3 pengguna)

| Peran | Email | Password (demo) | Masuk ke |
|---|---|---|---|
| Admin / Owner | `admin@debarrio.id` | `password` | `/admin/dashboard` |
| Kasir | `kasir@debarrio.id` | `password` | `/kasir` |
| Pelanggan | `pelanggan@debarrio.id` | `password` | `/pelanggan` |

> Password `password` hanya untuk demo dan pengujian. Ganti sebelum dipakai sungguhan.

### 2.2 Matriks hak akses (sesuai Figma A6)

| Fitur | Admin | Kasir | Pelanggan |
|---|:--:|:--:|:--:|
| Dashboard dan laporan penjualan | Ya | Tidak | Tidak |
| Kelola menu, stok, pengeluaran, meja | Ya | Tidak | Tidak |
| **Kelola pengguna dan hak akses** | **Ya** | Tidak | Tidak |
| Proses pesanan dan pembayaran (halaman kasir) | Ya | Ya | Tidak |
| Pesanan dan akun milik sendiri | Tidak | Tidak | Ya |
| Ubah kata sandi dan logout | Ya | Ya | Ya |

Pelanggan tetap bisa membuka halaman publik (landing page dan menu) tanpa login.

### 2.3 Perilaku akses

| Situasi | Hasil |
|---|---|
| Belum login membuka halaman terproteksi | Diarahkan ke `/login` |
| Login berhasil | Diarahkan ke halaman utama sesuai peran |
| Peran tidak sesuai (misalnya Kasir membuka `/admin/users`) | Halaman **403** |
| Akun nonaktif (`is_active = 0`) | Login ditolak |
| Sudah login membuka `/login` | Diarahkan ke halaman utama perannya |

---

## 3. Persiapan Proyek

```bash
composer create-project laravel/laravel warkop-de-barrio
cd warkop-de-barrio
composer require mallardduck/blade-lucide-icons   # ikon (opsional, boleh diganti SVG inline)
npm install
```

Atur `.env` (database `warkop_de_barrio`, lihat `Database_Warkop_De_Barrio.md`), lalu jalankan:

```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
npm run dev
php artisan serve
```

### Struktur berkas modul ini

```
app/
  Http/
    Controllers/
      AuthController.php
      ProfileController.php
      Admin/
        DashboardController.php
        UserController.php
    Middleware/RoleMiddleware.php
    Requests/
      LoginRequest.php
      RegisterRequest.php
      StoreUserRequest.php
      UpdateUserRequest.php
  Models/User.php
database/
  factories/UserFactory.php
  seeders/UserSeeder.php
resources/
  css/app.css
  views/
    layouts/{guest,admin,kasir,pelanggan}.blade.php
    auth/{login,register}.blade.php
    admin/dashboard.blade.php
    admin/users/{index,create,edit,_form}.blade.php
    kasir/index.blade.php
    pelanggan/{landing,beranda,akun}.blade.php
    profile/password.blade.php
    errors/403.blade.php
routes/web.php
tests/Feature/AccessTest.php
```

---

## 4. Database (tabel `users`)

Modul ini memakai tabel `users` dari `Database_Warkop_De_Barrio.md` dengan **satu penyesuaian**: peran `pelanggan`, yang belum ada di Laporan Progres 3 (hanya `admin` dan `kasir`). Tanpa penyesuaian ini, registrasi pelanggan dan akun demo pelanggan akan gagal (nilai `pelanggan` tidak ada di ENUM).

### 4.1 Kecocokan kolom

| Kolom | Di database (DB md) | Dipakai modul ini |
|---|---|---|
| `name` | VARCHAR(100) | Ya (validasi `max:100`) |
| `email` | VARCHAR(150), UNIQUE | Ya (validasi `max:150`, `unique`) |
| `password` | VARCHAR(255) | Ya (di-hash oleh cast `hashed`) |
| `role` | ENUM('admin','kasir') | **Perlu ditambah `'pelanggan'`** |
| `is_active` | TINYINT(1), default 1 | Ya (akun nonaktif ditolak login) |
| `remember_token` | Ada di tabel bawaan Laravel (Cara B), **tidak ada di skrip SQL Cara A** | Ya (checkbox "Ingat saya") |
| `created_at`, `updated_at` | TIMESTAMP | Ya |

### 4.2 Pilih sesuai kondisi Anda

**Kondisi 1: database belum dibuat (Cara B, migration).** Pada migration `add_role_to_users_table` di `Database_Warkop_De_Barrio.md`, langsung gunakan tiga peran:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'kasir', 'pelanggan'])->default('pelanggan')->after('password');
        $table->boolean('is_active')->default(true)->after('role');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['role', 'is_active']);
    });
}
```

**Kondisi 2: database sudah dibuat dari panduan database (peran hanya `admin` dan `kasir`).** Jangan membuat ulang `add_role_to_users_table` (kolom sudah ada, akan error *duplicate column*). Buat migration baru untuk menambah peran:

```bash
php artisan make:migration add_pelanggan_role_to_users_table
```

```php
use Illuminate\Support\Facades\DB;

public function up(): void
{
    DB::statement("ALTER TABLE users MODIFY role ENUM('admin','kasir','pelanggan') NOT NULL DEFAULT 'pelanggan'");
}

public function down(): void
{
    // Hapus dulu baris ber-role 'pelanggan' sebelum rollback, bila tidak, perintah ini gagal
    DB::statement("ALTER TABLE users MODIFY role ENUM('admin','kasir') NOT NULL DEFAULT 'kasir'");
}
```

**Kondisi 3: database dibuat dengan skrip SQL (Cara A).** Skrip SQL terbaru di `Database_Warkop_De_Barrio.md` sudah menyertakan kolom `remember_token` dan `email_verified_at`, jadi cukup tambahkan peran:

```sql
ALTER TABLE users
  MODIFY role ENUM('admin','kasir','pelanggan') NOT NULL DEFAULT 'pelanggan';
```

Jika tabel `users` Anda dibuat dari versi skrip yang lama (tanpa `remember_token`), tambahkan kolomnya dulu:

```sql
ALTER TABLE users
  ADD COLUMN remember_token VARCHAR(100) NULL AFTER is_active,
  ADD COLUMN email_verified_at TIMESTAMP NULL AFTER email;
```

Lalu pada `.env` ganti `SESSION_DRIVER=file`, karena skrip SQL tidak membuat tabel `sessions` (default Laravel 11+ adalah `database`). Dengan Cara A, `php artisan test` tidak bisa dipakai (butuh migration); gunakan Cara B bila ingin menjalankan feature test.

> Peran `pelanggan` adalah tambahan dari desain Figma (akun pelanggan opsional). Perbarui bagian tabel `users` dan use case pada laporan bila dipakai.

---

## 5. Model, Middleware, dan Konfigurasi

### 5.1 `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLES = ['admin', 'kasir', 'pelanggan'];

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',   // otomatis di-hash saat disimpan
            'is_active'         => 'boolean',
        ];
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /** Nama route halaman utama sesuai peran. */
    public function homeRoute(): string
    {
        return match ($this->role) {
            'admin'  => 'admin.dashboard',
            'kasir'  => 'kasir.index',
            default  => 'pelanggan.beranda',
        };
    }
}
```

> Karena cast `hashed` aktif, **jangan** memakai `Hash::make()` lagi saat menyimpan (akan ter-hash dua kali). Kirim password teks biasa ke `User::create()`.

### 5.2 `app/Http/Middleware/RoleMiddleware.php`

```bash
php artisan make:middleware RoleMiddleware
```

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active || ! $user->hasRole(...$roles)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
```

### 5.3 Daftarkan alias di `bootstrap/app.php`

```php
use App\Http\Middleware\RoleMiddleware;

->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => RoleMiddleware::class,
    ]);

    // Pengguna yang sudah login dan membuka /login diarahkan ke halaman perannya
    $middleware->redirectUsersTo(fn () => route(auth()->user()->homeRoute()));
})
```

---

## 6. Routes (`routes/web.php`)

```php
<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Publik
Route::view('/', 'pelanggan.landing')->name('landing');

// Tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// Semua peran yang sudah login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/akun/kata-sandi', [ProfileController::class, 'edit'])->name('password.edit');
    Route::put('/akun/kata-sandi', [ProfileController::class, 'update'])->name('password.update');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::resource('users', UserController::class)->except('show');
});

// Kasir (Admin juga boleh, sesuai matriks hak akses)
Route::middleware(['auth', 'role:kasir,admin'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::view('/', 'kasir.index')->name('index');
});

// Pelanggan
Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::view('/', 'pelanggan.beranda')->name('beranda');
    Route::view('/akun', 'pelanggan.akun')->name('akun');
});
```

> Ingin pemisahan ketat (Admin tidak boleh masuk halaman kasir)? Ubah `role:kasir,admin` menjadi `role:kasir`.

---

## 7. Controller dan Form Request

### 7.1 `AuthController`

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $credentials['is_active'] = true;   // akun nonaktif tidak bisa masuk

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi salah, atau akun Anda nonaktif.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        // Selalu ke halaman sesuai peran (bukan "intended") agar tidak berujung 403
        return redirect()->route(Auth::user()->homeRoute());
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /** Registrasi hanya untuk peran Pelanggan. */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            ...$request->validated(),
            'role'      => 'pelanggan',
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->homeRoute());
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah keluar.');
    }
}
```

### 7.2 Form Request

```bash
php artisan make:request LoginRequest
php artisan make:request RegisterRequest
php artisan make:request StoreUserRequest
php artisan make:request UpdateUserRequest
```

```php
// LoginRequest
public function authorize(): bool { return true; }
public function rules(): array
{
    return [
        'email'    => ['required', 'email'],
        'password' => ['required', 'string'],
    ];
}
public function messages(): array
{
    return [
        'email.required'    => 'Email wajib diisi.',
        'email.email'       => 'Format email tidak valid.',
        'password.required' => 'Kata sandi wajib diisi.',
    ];
}
```

```php
// RegisterRequest
public function authorize(): bool { return true; }
public function rules(): array
{
    return [
        'name'     => ['required', 'string', 'max:100'],
        'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ];
}
```

```php
// StoreUserRequest (Admin menambah pengguna)
public function authorize(): bool { return $this->user()?->hasRole('admin') ?? false; }
public function rules(): array
{
    return [
        'name'      => ['required', 'string', 'max:100'],
        'email'     => ['required', 'email', 'max:150', 'unique:users,email'],
        'password'  => ['required', 'string', 'min:8', 'confirmed'],
        'role'      => ['required', 'in:admin,kasir,pelanggan'],
        'is_active' => ['sometimes', 'boolean'],
    ];
}
```

```php
// UpdateUserRequest (password boleh kosong = tidak diubah)
public function authorize(): bool { return $this->user()?->hasRole('admin') ?? false; }
public function rules(): array
{
    return [
        'name'      => ['required', 'string', 'max:100'],
        'email'     => ['required', 'email', 'max:150', 'unique:users,email,' . $this->route('user')->id],
        'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
        'role'      => ['required', 'in:admin,kasir,pelanggan'],
        'is_active' => ['sometimes', 'boolean'],
    ];
}
```

### 7.3 `Admin/UserController` (Kelola Pengguna)

```bash
php artisan make:controller Admin/UserController --resource
php artisan make:controller Admin/DashboardController
```

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")))
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $counts = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');

        return view('admin.users.index', compact('users', 'counts'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([...$request->validated(), 'is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Admin tidak boleh menurunkan atau menonaktifkan dirinya sendiri
        if ($user->id === auth()->id()) {
            $data['role'] = $user->role;
            $data['is_active'] = true;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna diperbarui.');
    }

    public function toggle(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'Status pengguna diubah.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        try {
            $user->delete();
        } catch (QueryException) {
            // Foreign key RESTRICT: pengguna sudah tercatat di payments/expenses/stock_movements
            return back()->with('error', 'Pengguna memiliki riwayat pembayaran, pengeluaran, atau stok. Nonaktifkan saja akunnya.');
        }

        return back()->with('success', 'Pengguna dihapus.');
    }
}
```

`DashboardController`:

```php
public function index()
{
    return view('admin.dashboard');
}
```

### 7.4 `ProfileController` (ubah kata sandi, semua peran)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
        ]);

        $request->user()->update(['password' => $request->password]);

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }
}
```

---

## 8. Seeder dan Factory

`database/factories/UserFactory.php`, tambahkan di `definition()`:

```php
'role'      => 'pelanggan',
'is_active' => true,
```

`database/seeders/UserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Muhammad Fikri Firdaus', 'email' => 'admin@debarrio.id',     'role' => 'admin'],
            ['name' => 'Ayu Lestari',            'email' => 'kasir@debarrio.id',     'role' => 'kasir'],
            ['name' => 'Rina Anggraini',         'email' => 'pelanggan@debarrio.id', 'role' => 'pelanggan'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                $u + ['password' => 'password', 'is_active' => true]   // di-hash otomatis oleh cast
            );
        }
    }
}
```

---

## 9. Tampilan (Blade + Tailwind)

### 9.1 Token warna dan font: `resources/css/app.css` (Tailwind v4)

```css
@import "tailwindcss";

@theme {
  --font-sans: "Inter", ui-sans-serif, system-ui, sans-serif;

  --color-brand-900: #1F4D3A;
  --color-brand-700: #2E6B52;
  --color-brand-100: #E3EDE7;
  --color-cream: #F6EFD8;
  --color-cream-50: #FBF8EE;
  --color-ink: #1B2B24;
  --color-muted: #66776D;
  --color-line: #E6E0CC;
  --color-gold: #C58B2A;
  --color-danger: #B3402F;
  --color-danger-bg: #FBE7E3;
  --color-warn-bg: #FCEFD0;
  --color-warn-fg: #8A5A0B;
  --color-ok-bg: #DDEEE3;
}
```

> Jika memakai Tailwind v3, pindahkan warna ke `theme.extend.colors` di `tailwind.config.js` dengan nama yang sama (`brand: {900, 700, 100}`, dst.).

### 9.2 `layouts/guest.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Masuk') | Warkop De Barrio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-50 font-sans text-ink antialiased">
    @yield('content')
</body>
</html>
```

### 9.3 `auth/login.blade.php` (Figma K1 dan 05)

Layout dua panel di layar sedang ke atas (K1), dan kartu tunggal dengan tab Masuk/Daftar di mobile (05).

```blade
@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="flex min-h-screen">

    {{-- Panel merek (tablet/desktop) --}}
    <aside class="hidden w-[460px] shrink-0 flex-col justify-center gap-5 bg-brand-900 p-12 text-white md:flex">
        <div class="flex size-16 items-center justify-center rounded-full bg-cream text-brand-900">
            <x-lucide-coffee class="size-8" />
        </div>
        <h1 class="text-4xl font-bold">De Barrio</h1>
        <p class="text-[15px] leading-6 text-[#D7E5DC]">
            Masuk sesuai peran Anda: admin, kasir, atau pelanggan.
        </p>
        <ul class="space-y-3 text-sm font-medium text-[#D7E5DC]">
            @foreach (['Admin mengelola menu, stok, laporan, dan pengguna', 'Kasir menerima pesanan dan pembayaran', 'Pelanggan memesan dan melihat riwayat'] as $f)
                <li class="flex items-center gap-2.5">
                    <span class="flex size-[22px] items-center justify-center rounded-full bg-brand-700 text-cream">
                        <x-lucide-check class="size-3.5" />
                    </span>
                    {{ $f }}
                </li>
            @endforeach
        </ul>
    </aside>

    {{-- Form --}}
    <main class="flex flex-1 items-center justify-center px-6 py-10">
        <div class="w-full max-w-[380px] space-y-5">

            <div class="flex size-[52px] items-center justify-center rounded-full bg-brand-900 text-cream md:hidden">
                <x-lucide-coffee class="size-7" />
            </div>

            <div class="space-y-2">
                <h2 class="text-[26px] font-bold">Masuk</h2>
                <p class="text-sm text-muted">Gunakan akun yang terdaftar. Pelanggan tetap bisa memesan lewat QR tanpa masuk.</p>
            </div>

            {{-- Tab Masuk / Daftar (Figma 05) --}}
            <div class="flex rounded-xl bg-brand-100 p-1 text-sm">
                <a href="{{ route('login') }}" class="flex-1 rounded-[9px] bg-white py-2.5 text-center font-bold text-brand-900 shadow-sm">Masuk</a>
                <a href="{{ route('register') }}" class="flex-1 rounded-[9px] py-2.5 text-center font-semibold text-muted">Daftar</a>
            </div>

            @if (session('status'))
                <p class="rounded-xl bg-ok-bg px-3.5 py-2.5 text-sm font-medium text-brand-900">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-medium text-muted">Email</label>
                    <div class="flex items-center gap-2.5 rounded-xl border bg-white px-3.5 py-3 {{ $errors->has('email') ? 'border-danger' : 'border-line' }} focus-within:border-brand-900">
                        <x-lucide-mail class="size-[18px] text-muted" />
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                               placeholder="nama@email.com"
                               class="w-full bg-transparent text-sm outline-none placeholder:text-[#9AA59E]">
                    </div>
                    @error('email')
                        <p class="text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-medium text-muted">Kata sandi</label>
                    <div class="flex items-center gap-2.5 rounded-xl border border-line bg-white px-3.5 py-3 focus-within:border-brand-900">
                        <x-lucide-lock class="size-[18px] text-muted" />
                        <input id="password" name="password" type="password" required
                               placeholder="Masukkan kata sandi"
                               class="w-full bg-transparent text-sm outline-none placeholder:text-[#9AA59E]">
                    </div>
                    @error('password')
                        <p class="text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remember" class="size-4 rounded border-line accent-brand-900"> Ingat saya
                </label>

                <button type="submit"
                        class="w-full rounded-[14px] bg-brand-900 py-3.5 text-[15px] font-semibold text-white transition hover:bg-brand-700">
                    Masuk
                </button>
            </form>

            <a href="{{ route('landing') }}"
               class="block w-full rounded-[14px] border border-line bg-white py-3 text-center text-sm font-semibold text-brand-900">
                Lanjut tanpa akun
            </a>

            {{-- Bantuan demo: hanya tampil di lingkungan lokal. HAPUS saat produksi. --}}
            @if (app()->environment('local'))
                <div class="space-y-2 rounded-xl bg-warn-bg p-3.5">
                    <p class="text-xs font-semibold text-warn-fg">Akun demo (isi otomatis, lalu klik Masuk)</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach ([['Admin', 'admin@debarrio.id'], ['Kasir', 'kasir@debarrio.id'], ['Pelanggan', 'pelanggan@debarrio.id']] as [$label, $mail])
                            <button type="button" data-demo="{{ $mail }}"
                                    class="rounded-lg bg-white py-2 text-xs font-semibold text-ink ring-1 ring-line hover:ring-brand-900">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <script>
                    document.querySelectorAll('[data-demo]').forEach((b) =>
                        b.addEventListener('click', () => {
                            document.getElementById('email').value = b.dataset.demo;
                            document.getElementById('password').value = 'password';
                        })
                    );
                </script>
            @endif
        </div>
    </main>
</div>
@endsection
```

### 9.4 `auth/register.blade.php`

Salin `login.blade.php`, lalu ubah: tab **Daftar** menjadi aktif, `action` ke `route('register.store')`, dan tambahkan field **Nama**, **Email**, **Kata sandi**, **Konfirmasi kata sandi** (`name="password_confirmation"`). Tombol berlabel **Daftar**. Hapus blok akun demo.

### 9.5 Layout per peran

Tiap peran punya kerangka berbeda sesuai Figma:

| Peran | Layout | Ciri (dari Figma) |
|---|---|---|
| Admin | `layouts/admin.blade.php` | Sidebar hijau 240 px, profil di bawah, konten `cream-50` |
| Kasir | `layouts/kasir.blade.php` | Rail ikon hijau 96 px, area kerja di kanan |
| Pelanggan | `layouts/pelanggan.blade.php` | Lebar mobile 390 px, bottom nav (Beranda, Menu, Pesanan, Akun) |

**`layouts/admin.blade.php`** (menu lain ditandai "belum tersedia" karena dikerjakan anggota lain):

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Admin De Barrio</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-50 font-sans text-ink antialiased">
@php
    $menu = [
        ['Dashboard',       'lucide-layout-dashboard', 'admin.dashboard', 'admin.dashboard'],
        ['Menu & Kategori', 'lucide-coffee',           null, null],
        ['Stok Bahan',      'lucide-package',          null, null],
        ['Pengeluaran',     'lucide-wallet',           null, null],
        ['Laporan',         'lucide-chart-column',     null, null],
        ['Pengguna',        'lucide-users',            'admin.users.index', 'admin.users.*'],
        ['Meja & QR',       'lucide-qr-code',          null, null],
    ];
@endphp
<div class="flex min-h-screen">
    <aside class="flex w-60 shrink-0 flex-col gap-1.5 bg-brand-900 p-4">
        <div class="flex items-center gap-2.5 px-2 pb-5 pt-2">
            <span class="flex size-10 items-center justify-center rounded-full bg-cream text-brand-900"><x-lucide-coffee class="size-[22px]" /></span>
            <div>
                <p class="font-bold leading-tight text-white">De Barrio</p>
                <p class="text-[11px] font-medium text-[#A9C4B5]">Panel Admin</p>
            </div>
        </div>

        @foreach ($menu as [$label, $icon, $route, $pattern])
            @php $active = $pattern && request()->routeIs($pattern); @endphp
            <a href="{{ $route ? route($route) : '#' }}"
               @if(! $route) title="Belum tersedia" @endif
               class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm {{ $active ? 'bg-brand-700 font-bold text-white' : 'font-medium text-[#A9C4B5] hover:bg-brand-700/50' }} {{ $route ? '' : 'cursor-not-allowed opacity-60' }}">
                <x-dynamic-component :component="$icon" class="size-5" />
                {{ $label }}
            </a>
        @endforeach

        <div class="mt-auto flex items-center gap-2.5 rounded-xl bg-brand-700 p-3">
            <span class="flex size-9 items-center justify-center rounded-full bg-cream text-xs font-bold text-brand-900">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-bold text-white">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-[#BFD3C7]">Admin / Owner</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button title="Keluar" class="text-[#BFD3C7] hover:text-white"><x-lucide-log-out class="size-4" /></button>
            </form>
        </div>
    </aside>

    <main class="flex-1 space-y-5 px-8 py-7">
        @if (session('success'))
            <p class="rounded-xl bg-ok-bg px-4 py-3 text-sm font-medium text-brand-900">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p class="rounded-xl bg-danger-bg px-4 py-3 text-sm font-medium text-danger">{{ session('error') }}</p>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
```

**`layouts/kasir.blade.php`** dan **`layouts/pelanggan.blade.php`**: kerangka sama (head dan font Inter), dengan perbedaan:

- Kasir: `<aside class="flex w-24 flex-col items-center gap-2 bg-brand-900 py-5">` berisi logo bulat krem, empat item ikon + label 11 px (Kasir, Pesanan QR, Riwayat, Sinkron), dan tombol Keluar (POST `logout`) di bawah.
- Pelanggan: `<div class="mx-auto min-h-screen max-w-[390px] bg-cream-50">`, konten di tengah, dan `<nav class="fixed inset-x-0 bottom-0 mx-auto flex max-w-[390px] border-t border-line bg-white py-2.5">` berisi Beranda, Menu, Pesanan, Akun (ikon 22 px, label 11 px, aktif `text-brand-900 font-bold`).

### 9.6 Halaman utama tiap peran (bukti "masuk ke tampilan masing-masing")

Tiga halaman ini cukup berupa kerangka yang menampilkan nama, peran, dan menu. Isi lengkapnya dikerjakan anggota tim lain.

`admin/dashboard.blade.php`:

```blade
@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div>
        <h1 class="text-[26px] font-bold">Dashboard</h1>
        <p class="text-[13px] text-muted">Selamat datang, {{ auth()->user()->name }} (Admin / Owner)</p>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <a href="{{ route('admin.users.index') }}" class="rounded-[14px] border border-line bg-white p-5 hover:border-brand-900">
            <x-lucide-users class="size-6 text-brand-900" />
            <p class="mt-3 font-bold">Kelola Pengguna</p>
            <p class="text-sm text-muted">Tambah, ubah, dan nonaktifkan akun.</p>
        </a>
    </div>
@endsection
```

`kasir/index.blade.php`:

```blade
@extends('layouts.kasir')
@section('title', 'Kasir')

@section('content')
    <h1 class="text-[22px] font-bold">Pesanan Baru</h1>
    <p class="text-[13px] text-muted">Masuk sebagai {{ auth()->user()->name }} (Kasir)</p>
    {{-- Isi halaman kasir: dikerjakan anggota tim lain (Figma K2) --}}
@endsection
```

`pelanggan/beranda.blade.php`:

```blade
@extends('layouts.pelanggan')
@section('title', 'Pesanan Saya')

@section('content')
    <h1 class="px-5 pt-5 text-[22px] font-bold">Pesanan Saya</h1>
    <p class="px-5 text-[13px] text-muted">Halo, {{ auth()->user()->name }} (Pelanggan)</p>
    {{-- Daftar pesanan: dikerjakan anggota tim lain (Figma 06) --}}
@endsection
```

### 9.7 Kelola Pengguna: `admin/users/index.blade.php` (Figma A6)

```blade
@extends('layouts.admin')
@section('title', 'Pengguna')

@php
    $badge = [
        'admin'     => 'bg-brand-900 text-white',
        'kasir'     => 'bg-warn-bg text-warn-fg',
        'pelanggan' => 'bg-brand-100 text-brand-900',
    ];
    $chips = [null => 'Semua '.$counts->sum(), 'admin' => 'Admin '.($counts['admin'] ?? 0), 'kasir' => 'Kasir '.($counts['kasir'] ?? 0), 'pelanggan' => 'Pelanggan '.($counts['pelanggan'] ?? 0)];
@endphp

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-[26px] font-bold">Pengguna & Hak Akses</h1>
            <p class="text-[13px] text-muted">Kelola akun dan peran (Admin, Kasir, Pelanggan)</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="flex items-center gap-2 rounded-[10px] bg-brand-900 px-4 py-2.5 text-[13px] font-semibold text-white">
            <x-lucide-plus class="size-4" /> Tambah Pengguna
        </a>
    </div>

    {{-- Filter peran + pencarian --}}
    <div class="flex items-center gap-2">
        @foreach ($chips as $value => $label)
            @php $on = (request('role') ?? null) === $value; @endphp
            <a href="{{ route('admin.users.index', array_filter(['role' => $value, 'q' => request('q')])) }}"
               class="rounded-full px-3.5 py-2 text-[13px] font-semibold {{ $on ? 'bg-brand-900 text-white' : 'border border-line bg-white' }}">
                {{ $label }}
            </a>
        @endforeach

        <form method="GET" class="ml-auto w-64">
            <input type="hidden" name="role" value="{{ request('role') }}">
            <div class="flex items-center gap-2.5 rounded-[10px] border border-line bg-white px-3.5 py-2.5">
                <x-lucide-search class="size-4 text-muted" />
                <input name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..."
                       class="w-full bg-transparent text-[13px] outline-none placeholder:text-[#9AA59E]">
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="overflow-hidden rounded-[14px] border border-line bg-white">
        <table class="w-full text-[13px]">
            <thead class="bg-brand-100 text-left font-semibold text-brand-900">
                <tr>
                    <th class="px-4 py-2.5">Pengguna</th>
                    <th class="px-4 py-2.5">Email</th>
                    <th class="px-4 py-2.5">Peran</th>
                    <th class="px-4 py-2.5">Status</th>
                    <th class="px-4 py-2.5">Terakhir diubah</th>
                    <th class="px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($users as $u)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex size-[34px] items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-900">
                                    {{ strtoupper(collect(explode(' ', $u->name))->take(2)->map(fn ($w) => $w[0])->join('')) }}
                                </span>
                                <span class="font-semibold">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">{{ $u->email }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $badge[$u->role] }}">{{ ucfirst($u->role) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.users.toggle', $u) }}">
                                @csrf @method('PATCH')
                                <button class="flex items-center gap-2 text-xs font-medium {{ $u->is_active ? 'text-brand-900' : 'text-muted' }}"
                                        @disabled($u->id === auth()->id())>
                                    <span class="flex h-5 w-[34px] items-center rounded-full p-0.5 {{ $u->is_active ? 'justify-end bg-brand-900' : 'justify-start bg-[#C9CFCB]' }}">
                                        <span class="size-4 rounded-full bg-white"></span>
                                    </span>
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-muted">{{ $u->updated_at?->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.users.edit', $u) }}" title="Ubah"><x-lucide-pencil class="size-4 text-muted hover:text-ink" /></a>
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                          onsubmit="return confirm('Hapus pengguna {{ $u->name }}?')">
                                        @csrf @method('DELETE')
                                        <button title="Hapus"><x-lucide-trash-2 class="size-4 text-danger" /></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-muted">Tidak ada pengguna ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}

    {{-- Matriks hak akses (Figma A6) --}}
    <div class="space-y-2.5 rounded-[14px] border border-line bg-white p-[18px]">
        <h2 class="font-bold">Hak akses per peran</h2>
        <table class="w-full overflow-hidden rounded-[14px] border border-line text-[13px]">
            <thead class="bg-brand-100 text-left font-semibold text-brand-900">
                <tr><th class="px-4 py-2.5">Fitur</th><th class="w-24 px-4 py-2.5">Admin</th><th class="w-24 px-4 py-2.5">Kasir</th><th class="w-24 px-4 py-2.5">Pelanggan</th></tr>
            </thead>
            <tbody class="divide-y divide-line">
                @foreach ([
                    ['Kelola menu, stok, pengeluaran, meja', 1, 0, 0],
                    ['Dashboard dan laporan penjualan',      1, 0, 0],
                    ['Proses pesanan dan pembayaran',        1, 1, 0],
                    ['Kelola pengguna dan hak akses',        1, 0, 0],
                    ['Riwayat pesanan sendiri',              0, 0, 1],
                ] as [$fitur, $a, $k, $p])
                    <tr>
                        <td class="px-4 py-2.5">{{ $fitur }}</td>
                        @foreach ([$a, $k, $p] as $ok)
                            <td class="px-4 py-2.5">
                                @if ($ok) <x-lucide-check class="size-4 text-brand-900" /> @else <span class="text-muted">-</span> @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
```

### 9.8 Form tambah/ubah: `admin/users/_form.blade.php`

```blade
@php $isEdit = isset($user); @endphp

<form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}"
      class="max-w-xl space-y-4 rounded-[14px] border border-line bg-white p-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    @foreach ([['name', 'Nama lengkap', 'text'], ['email', 'Email', 'email']] as [$f, $label, $type])
        <div class="space-y-1.5">
            <label class="text-xs font-medium text-muted">{{ $label }}</label>
            <input type="{{ $type }}" name="{{ $f }}" value="{{ old($f, $user->$f ?? '') }}"
                   class="w-full rounded-xl border px-3.5 py-3 text-sm outline-none focus:border-brand-900 {{ $errors->has($f) ? 'border-danger' : 'border-line' }}">
            @error($f) <p class="text-xs font-medium text-danger">{{ $message }}</p> @enderror
        </div>
    @endforeach

    <div class="space-y-1.5">
        <label class="text-xs font-medium text-muted">Peran</label>
        <select name="role" class="w-full rounded-xl border border-line bg-white px-3.5 py-3 text-sm">
            @foreach (['admin' => 'Admin / Owner', 'kasir' => 'Kasir', 'pelanggan' => 'Pelanggan'] as $v => $l)
                <option value="{{ $v }}" @selected(old('role', $user->role ?? 'kasir') === $v)>{{ $l }}</option>
            @endforeach
        </select>
        @error('role') <p class="text-xs font-medium text-danger">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1.5">
            <label class="text-xs font-medium text-muted">Kata sandi {{ $isEdit ? '(kosongkan bila tidak diubah)' : '' }}</label>
            <input type="password" name="password" class="w-full rounded-xl border px-3.5 py-3 text-sm outline-none focus:border-brand-900 {{ $errors->has('password') ? 'border-danger' : 'border-line' }}">
            @error('password') <p class="text-xs font-medium text-danger">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-1.5">
            <label class="text-xs font-medium text-muted">Konfirmasi kata sandi</label>
            <input type="password" name="password_confirmation" class="w-full rounded-xl border border-line px-3.5 py-3 text-sm outline-none focus:border-brand-900">
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="size-4 accent-brand-900"
               @checked(old('is_active', $user->is_active ?? true))> Akun aktif
    </label>

    <div class="flex gap-2.5 pt-2">
        <a href="{{ route('admin.users.index') }}" class="rounded-[10px] border border-line px-4 py-2.5 text-[13px] font-semibold">Batal</a>
        <button class="rounded-[10px] bg-brand-900 px-5 py-2.5 text-[13px] font-semibold text-white">Simpan</button>
    </div>
</form>
```

`create.blade.php` dan `edit.blade.php`:

```blade
@extends('layouts.admin')
@section('title', 'Tambah Pengguna')   {{-- edit: 'Ubah Pengguna' --}}
@section('content')
    <h1 class="text-[26px] font-bold">Tambah Pengguna</h1>   {{-- edit: Ubah Pengguna --}}
    @include('admin.users._form')
@endsection
```

### 9.9 Halaman 403: `errors/403.blade.php`

```blade
@extends('layouts.guest')
@section('title', 'Akses ditolak')
@section('content')
<div class="flex min-h-screen flex-col items-center justify-center gap-3 px-6 text-center">
    <span class="flex size-16 items-center justify-center rounded-full bg-danger-bg text-danger"><x-lucide-lock class="size-8" /></span>
    <h1 class="text-2xl font-bold">Akses ditolak</h1>
    <p class="max-w-sm text-sm text-muted">{{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses ke halaman ini.' }}</p>
    @auth
        <a href="{{ route(auth()->user()->homeRoute()) }}" class="rounded-[14px] bg-brand-900 px-5 py-3 text-sm font-semibold text-white">Kembali ke halaman saya</a>
    @else
        <a href="{{ route('login') }}" class="rounded-[14px] bg-brand-900 px-5 py-3 text-sm font-semibold text-white">Masuk</a>
    @endauth
</div>
@endsection
```

### 9.10 Ubah kata sandi: `profile/password.blade.php`

Pakai layout sesuai peran (`@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : (auth()->user()->hasRole('kasir') ? 'layouts.kasir' : 'layouts.pelanggan'))`) dengan tiga field: `current_password`, `password`, `password_confirmation`, dan tombol **Simpan**. Tampilkan `session('success')` dan error validasi seperti pada form pengguna.

---

## 10. Pengujian

### 10.1 Pengujian manual

| # | Langkah | Hasil yang diharapkan |
|---|---|---|
| 1 | Login `admin@debarrio.id` | Masuk ke `/admin/dashboard`, sidebar hijau 240 px |
| 2 | Login `kasir@debarrio.id` | Masuk ke `/kasir`, tampilan rail kasir |
| 3 | Login `pelanggan@debarrio.id` | Masuk ke `/pelanggan`, tampilan mobile |
| 4 | Kasir membuka `/admin/users` | Halaman **403** |
| 5 | Pelanggan membuka `/kasir` | Halaman **403** |
| 6 | Belum login membuka `/admin/users` | Diarahkan ke `/login` |
| 7 | Login dengan password salah | Pesan "Email atau kata sandi salah..." |
| 8 | Admin menonaktifkan Kasir, lalu Kasir login | Login ditolak |
| 9 | Admin menambah pengguna baru dengan email yang sudah ada | Error validasi "email has already been taken" |
| 10 | Admin mencoba menonaktifkan atau menghapus akun sendiri | Ditolak dengan pesan |
| 11 | Cari "ayu" dan filter peran Kasir | Daftar tersaring, paginasi tetap membawa filter |
| 12 | Klik Keluar | Kembali ke `/login`, sesi berakhir |

### 10.2 Feature test: `tests/Feature/AccessTest.php`

```bash
php artisan make:test AccessTest
```

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    private function userWith(string $role, bool $active = true): User
    {
        return User::factory()->create(['role' => $role, 'is_active' => $active, 'password' => 'password']);
    }

    public function test_login_mengarah_ke_halaman_sesuai_peran(): void
    {
        foreach (['admin' => 'admin.dashboard', 'kasir' => 'kasir.index', 'pelanggan' => 'pelanggan.beranda'] as $role => $route) {
            $user = $this->userWith($role);

            $this->post('/login', ['email' => $user->email, 'password' => 'password'])
                 ->assertRedirect(route($route));

            $this->post('/logout');
        }
    }

    public function test_akun_nonaktif_tidak_bisa_login(): void
    {
        $user = $this->userWith('kasir', active: false);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
    }

    public function test_kasir_tidak_boleh_membuka_kelola_pengguna(): void
    {
        $this->actingAs($this->userWith('kasir'))->get('/admin/users')->assertForbidden();
    }

    public function test_pelanggan_tidak_boleh_membuka_halaman_kasir(): void
    {
        $this->actingAs($this->userWith('pelanggan'))->get('/kasir')->assertForbidden();
    }

    public function test_admin_boleh_membuka_kelola_pengguna_dan_kasir(): void
    {
        $admin = $this->userWith('admin');

        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/kasir')->assertOk();
    }

    public function test_admin_dapat_menambah_pengguna(): void
    {
        $this->actingAs($this->userWith('admin'))
             ->post('/admin/users', [
                 'name' => 'Dimas Pratama', 'email' => 'dimas@debarrio.id',
                 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
                 'role' => 'kasir', 'is_active' => 1,
             ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'dimas@debarrio.id', 'role' => 'kasir']);
    }
}
```

Jalankan:

```bash
php artisan test
```

---

## 11. Pemetaan ke Checklist Spesifikasi Proyek

| Kode | Persyaratan | Dipenuhi oleh |
|---|---|---|
| W1 | Login/logout, password ter-hash, ubah kata sandi | `AuthController`, cast `hashed`, `ProfileController` |
| W2 | Tiga peran dengan menu dan rute terbatas | `RoleMiddleware`, route group per peran, tiga layout |
| W3 | Kelola pengguna (tambah, ubah, nonaktifkan, atur peran) | `Admin/UserController`, halaman Pengguna |
| W4 | Form Request untuk validasi | `LoginRequest`, `RegisterRequest`, `StoreUserRequest`, `UpdateUserRequest` |
| W7 | Pencarian, filter, paginasi | Filter peran, pencarian nama/email, `paginate(10)` |
| W11 | Seeder dan factory | `UserSeeder`, `UserFactory` |
| W12 | Git dan README | Lihat bagian 12 |
| P4 (pilihan) | Feature test | `tests/Feature/AccessTest.php` |

---

## 12. Urutan Pengerjaan dan Commit Git

Commit kecil dan bertahap agar riwayat Git terlihat jelas:

| # | Pekerjaan | Contoh pesan commit |
|---|---|---|
| 1 | Setup proyek, `.env`, Tailwind, token warna | `chore: setup laravel, tailwind, design tokens` |
| 2 | Migration peran dan status pengguna, model `User` | `feat(user): tambah role dan is_active` |
| 3 | `UserSeeder` dan factory | `feat(user): seeder 3 akun demo` |
| 4 | `AuthController`, `LoginRequest`, halaman login | `feat(auth): login dan logout` |
| 5 | `RoleMiddleware` dan route group per peran | `feat(auth): akses berdasarkan peran` |
| 6 | Layout admin, kasir, pelanggan dan halaman utama | `feat(ui): layout per peran` |
| 7 | CRUD pengguna, pencarian, filter | `feat(user): kelola pengguna` |
| 8 | Registrasi pelanggan dan ubah kata sandi | `feat(auth): registrasi dan ubah kata sandi` |
| 9 | Halaman 403 dan feature test | `test(auth): uji hak akses per peran` |
| 10 | README (cara menjalankan dan akun demo) | `docs: readme modul user management` |

Isi minimal `README.md`: deskripsi proyek, kebutuhan sistem, langkah instalasi (`composer install`, `npm install`, `.env`, `php artisan migrate --seed`, `npm run dev`, `php artisan serve`), tabel akun demo, dan daftar fitur modul ini.

---

## 13. Catatan Penting

- **Password demo** (`password`) dan tombol akun demo di halaman login hanya untuk lingkungan lokal. Hapus atau ganti sebelum publikasi.
- **Peran Pelanggan** belum ada di Laporan Progres 3 (hanya `admin` dan `kasir`). Perbarui bagian tabel `users` dan use case pada laporan bila peran ini dipakai.
- **Halaman lain** (menu, stok, laporan, kasir, pesanan) dikerjakan anggota tim lain. Menu yang belum tersedia di sidebar ditandai nonaktif.
- **Hapus pengguna** akan gagal bila pengguna sudah tercatat di `payments`, `expenses`, atau `stock_movements` (foreign key `RESTRICT`). Untuk `orders`, relasinya `SET NULL`, sehingga pesanan tetap ada dan kolom `processed_by` menjadi kosong. Ini sesuai rancangan database; untuk akun yang punya riwayat, gunakan tombol nonaktifkan.
- **Versi:** kode ditulis untuk Laravel 11/12 (middleware didaftarkan di `bootstrap/app.php`) dan Tailwind v4. Untuk Laravel 10, daftarkan alias middleware di `app/Http/Kernel.php` (`$middlewareAliases`).
