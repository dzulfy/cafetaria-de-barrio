# Panduan Pembuatan Database: Warkop Cafetaria De Barrio

Dokumen ini berisi skrip pembuatan database sesuai **Laporan Progres Minggu 3 (bagian 4: Struktur Data)**, yaitu 11 tabel untuk MySQL dengan konvensi penamaan Laravel.

Tersedia dua cara, pilih salah satu:

- **Cara A:** Skrip SQL langsung (cepat, cocok untuk uji coba di phpMyAdmin atau MySQL CLI).
- **Cara B:** Laravel Migration (disarankan untuk proyek, karena mengikuti framework dan bisa di-*rollback*).

> Jangan menjalankan Cara A dan Cara B pada database yang sama. Pilih salah satu.
>
> Jika memakai **Cara A**, set `SESSION_DRIVER=file` pada `.env`, karena skrip SQL tidak membuat tabel `sessions` yang dipakai Laravel secara default. Untuk modul login/user management, peran `pelanggan` juga perlu ditambahkan (lihat bagian 8).

---

## 1. Daftar Tabel

| No | Tabel | Fungsi |
|----|-------|--------|
| 1 | `users` | Akun admin dan kasir |
| 2 | `categories` | Kategori menu |
| 3 | `menus` | Daftar menu dan harga |
| 4 | `dining_tables` | Data meja dan token QR code |
| 5 | `orders` | Pesanan (QR dan kasir) |
| 6 | `order_items` | Rincian item pesanan |
| 7 | `payments` | Pembayaran pesanan |
| 8 | `ingredients` | Stok bahan |
| 9 | `menu_ingredients` | Takaran bahan per menu (opsional) |
| 10 | `stock_movements` | Riwayat mutasi stok |
| 11 | `expenses` | Pengeluaran usaha |

### Relasi

```
categories 1 --- N menus
menus      1 --- N order_items
orders     1 --- N order_items
orders     1 --- 1 payments
dining_tables 1 --- N orders
users      1 --- N orders          (orders.processed_by)
users      1 --- N payments
users      1 --- N expenses
users      1 --- N stock_movements
ingredients 1 --- N stock_movements
menus N --- M ingredients          (melalui menu_ingredients)
```

---

## 2. Prasyarat

- MySQL 8.0+ atau MariaDB 10.4+ (Laragon atau XAMPP)
- Untuk Cara B: PHP 8.2+, Composer, dan proyek Laravel

---

## 3. Cara A: Skrip SQL

Simpan sebagai `database.sql`, lalu jalankan:

```bash
mysql -u root -p < database.sql
```

Atau tempel ke tab **SQL** di phpMyAdmin.

```sql
-- =====================================================
-- Database Warkop Cafetaria De Barrio
-- Sumber: Laporan Progres Minggu 3, bagian 4
-- =====================================================
CREATE DATABASE IF NOT EXISTS warkop_de_barrio
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE warkop_de_barrio;

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- 1. users
-- -----------------------------------------------------
CREATE TABLE users (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(100)    NOT NULL,
  email       VARCHAR(150)    NOT NULL,
  password    VARCHAR(255)    NOT NULL,
  role        ENUM('admin','kasir') NOT NULL DEFAULT 'kasir',
  is_active   TINYINT(1)      NOT NULL DEFAULT 1,
  remember_token    VARCHAR(100) NULL,   -- tambahan Laravel (fitur "Ingat saya")
  email_verified_at TIMESTAMP    NULL,   -- tambahan Laravel (kompatibilitas factory)
  created_at  TIMESTAMP       NULL,
  updated_at  TIMESTAMP       NULL,
  PRIMARY KEY (id),
  UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 2. categories
-- -----------------------------------------------------
CREATE TABLE categories (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(80)     NOT NULL,
  sort_order  SMALLINT        NOT NULL DEFAULT 0,
  created_at  TIMESTAMP       NULL,
  updated_at  TIMESTAMP       NULL,
  PRIMARY KEY (id),
  UNIQUE KEY categories_name_unique (name)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 3. menus
-- -----------------------------------------------------
CREATE TABLE menus (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id   BIGINT UNSIGNED NOT NULL,
  name          VARCHAR(120)    NOT NULL,
  description   TEXT            NULL,
  price         DECIMAL(10,2)   NOT NULL,
  photo         VARCHAR(255)    NULL,
  is_available  TINYINT(1)      NOT NULL DEFAULT 1,
  created_at    TIMESTAMP       NULL,
  updated_at    TIMESTAMP       NULL,
  PRIMARY KEY (id),
  KEY menus_category_id_index (category_id),
  CONSTRAINT menus_category_id_foreign FOREIGN KEY (category_id)
    REFERENCES categories (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 4. dining_tables
-- -----------------------------------------------------
CREATE TABLE dining_tables (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(30)     NOT NULL,
  qr_token    VARCHAR(64)     NOT NULL,
  is_active   TINYINT(1)      NOT NULL DEFAULT 1,
  created_at  TIMESTAMP       NULL,
  updated_at  TIMESTAMP       NULL,
  PRIMARY KEY (id),
  UNIQUE KEY dining_tables_name_unique (name),
  UNIQUE KEY dining_tables_qr_token_unique (qr_token)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 5. orders
-- -----------------------------------------------------
CREATE TABLE orders (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_code       VARCHAR(20)     NOT NULL,
  dining_table_id  BIGINT UNSIGNED NULL,
  processed_by     BIGINT UNSIGNED NULL,
  source           ENUM('qr','kasir') NOT NULL,
  status           ENUM('menunggu','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  customer_name    VARCHAR(80)     NULL,
  total_amount     DECIMAL(12,2)   NOT NULL DEFAULT 0,
  note             VARCHAR(255)    NULL,
  client_uuid      CHAR(36)        NULL,
  ordered_at       DATETIME        NOT NULL,
  created_at       TIMESTAMP       NULL,
  updated_at       TIMESTAMP       NULL,
  PRIMARY KEY (id),
  UNIQUE KEY orders_order_code_unique (order_code),
  UNIQUE KEY orders_client_uuid_unique (client_uuid),
  KEY orders_status_index (status),
  KEY orders_ordered_at_index (ordered_at),
  CONSTRAINT orders_dining_table_id_foreign FOREIGN KEY (dining_table_id)
    REFERENCES dining_tables (id) ON DELETE SET NULL,
  CONSTRAINT orders_processed_by_foreign FOREIGN KEY (processed_by)
    REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 6. order_items
-- -----------------------------------------------------
CREATE TABLE order_items (
  id          BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
  order_id    BIGINT UNSIGNED   NOT NULL,
  menu_id     BIGINT UNSIGNED   NOT NULL,
  menu_name   VARCHAR(120)      NOT NULL,
  unit_price  DECIMAL(10,2)     NOT NULL,
  quantity    SMALLINT UNSIGNED NOT NULL,
  subtotal    DECIMAL(12,2)     NOT NULL,
  note        VARCHAR(255)      NULL,
  PRIMARY KEY (id),
  KEY order_items_order_id_index (order_id),
  KEY order_items_menu_id_index (menu_id),
  CONSTRAINT order_items_order_id_foreign FOREIGN KEY (order_id)
    REFERENCES orders (id) ON DELETE CASCADE,
  CONSTRAINT order_items_menu_id_foreign FOREIGN KEY (menu_id)
    REFERENCES menus (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 7. payments
-- -----------------------------------------------------
CREATE TABLE payments (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id       BIGINT UNSIGNED NOT NULL,
  user_id        BIGINT UNSIGNED NOT NULL,
  method         ENUM('tunai','qris','transfer') NOT NULL,
  amount_paid    DECIMAL(12,2)   NOT NULL,
  change_amount  DECIMAL(12,2)   NOT NULL DEFAULT 0,
  paid_at        DATETIME        NOT NULL,
  created_at     TIMESTAMP       NULL,
  updated_at     TIMESTAMP       NULL,
  PRIMARY KEY (id),
  UNIQUE KEY payments_order_id_unique (order_id),
  KEY payments_user_id_index (user_id),
  CONSTRAINT payments_order_id_foreign FOREIGN KEY (order_id)
    REFERENCES orders (id) ON DELETE RESTRICT,
  CONSTRAINT payments_user_id_foreign FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 8. ingredients
-- -----------------------------------------------------
CREATE TABLE ingredients (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(100)    NOT NULL,
  unit        VARCHAR(20)     NOT NULL,
  stock       DECIMAL(12,2)   NOT NULL DEFAULT 0,
  min_stock   DECIMAL(12,2)   NOT NULL DEFAULT 0,
  created_at  TIMESTAMP       NULL,
  updated_at  TIMESTAMP       NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 9. menu_ingredients (opsional: takaran bahan per menu)
-- -----------------------------------------------------
CREATE TABLE menu_ingredients (
  menu_id        BIGINT UNSIGNED NOT NULL,
  ingredient_id  BIGINT UNSIGNED NOT NULL,
  qty_used       DECIMAL(10,2)   NOT NULL,
  PRIMARY KEY (menu_id, ingredient_id),
  KEY menu_ingredients_ingredient_id_index (ingredient_id),
  CONSTRAINT menu_ingredients_menu_id_foreign FOREIGN KEY (menu_id)
    REFERENCES menus (id) ON DELETE CASCADE,
  CONSTRAINT menu_ingredients_ingredient_id_foreign FOREIGN KEY (ingredient_id)
    REFERENCES ingredients (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 10. stock_movements
-- -----------------------------------------------------
CREATE TABLE stock_movements (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ingredient_id  BIGINT UNSIGNED NOT NULL,
  user_id        BIGINT UNSIGNED NOT NULL,
  type           ENUM('masuk','keluar','penyesuaian') NOT NULL,
  quantity       DECIMAL(12,2)   NOT NULL,
  note           VARCHAR(255)    NULL,
  created_at     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY stock_movements_ingredient_id_index (ingredient_id),
  KEY stock_movements_user_id_index (user_id),
  CONSTRAINT stock_movements_ingredient_id_foreign FOREIGN KEY (ingredient_id)
    REFERENCES ingredients (id) ON DELETE RESTRICT,
  CONSTRAINT stock_movements_user_id_foreign FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 11. expenses
-- -----------------------------------------------------
CREATE TABLE expenses (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id       BIGINT UNSIGNED NOT NULL,
  description   VARCHAR(150)    NOT NULL,
  amount        DECIMAL(12,2)   NOT NULL,
  expense_date  DATE            NOT NULL,
  note          VARCHAR(255)    NULL,
  created_at    TIMESTAMP       NULL,
  updated_at    TIMESTAMP       NULL,
  PRIMARY KEY (id),
  KEY expenses_user_id_index (user_id),
  KEY expenses_expense_date_index (expense_date),
  CONSTRAINT expenses_user_id_foreign FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
```

### Data awal contoh (opsional)

Akun pengguna **jangan** diisi dengan password teks biasa. Buat lewat Laravel Seeder (`Hash::make()`), lihat bagian 5.

```sql
USE warkop_de_barrio;

INSERT INTO categories (name, sort_order, created_at, updated_at) VALUES
  ('Kopi', 1, NOW(), NOW()),
  ('Non-Kopi', 2, NOW(), NOW()),
  ('Makanan Ringan', 3, NOW(), NOW()),
  ('Makanan Berat', 4, NOW(), NOW());

INSERT INTO dining_tables (name, qr_token, is_active, created_at, updated_at) VALUES
  ('Meja 01', SUBSTRING(MD5(RAND()), 1, 32), 1, NOW(), NOW()),
  ('Meja 02', SUBSTRING(MD5(RAND()), 1, 32), 1, NOW(), NOW()),
  ('Meja 03', SUBSTRING(MD5(RAND()), 1, 32), 1, NOW(), NOW());
```

---

## 4. Cara B: Laravel Migration

### 4.1 Persiapan

```bash
composer create-project laravel/laravel warkop-de-barrio
cd warkop-de-barrio
```

Atur `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warkop_de_barrio
DB_USERNAME=root
DB_PASSWORD=
```

Buat database kosongnya:

```sql
CREATE DATABASE warkop_de_barrio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4.2 Membuat file migration

Urutan pembuatan penting karena ada *foreign key*. Jalankan berurutan:

```bash
php artisan make:migration add_role_to_users_table --table=users
php artisan make:migration create_categories_table
php artisan make:migration create_menus_table
php artisan make:migration create_dining_tables_table
php artisan make:migration create_orders_table
php artisan make:migration create_order_items_table
php artisan make:migration create_payments_table
php artisan make:migration create_ingredients_table
php artisan make:migration create_menu_ingredients_table
php artisan make:migration create_stock_movements_table
php artisan make:migration create_expenses_table
```

Tabel `users` sudah dibuat oleh migration bawaan Laravel, sehingga cukup menambahkan kolom `role` dan `is_active`.

### 4.3 Isi migration

Isi method `up()` dan `down()` setiap file dengan kode berikut. Semua file memakai:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
```

**`add_role_to_users_table`**

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'kasir'])->default('kasir')->after('password');
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

**`create_categories_table`**

```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name', 80)->unique();
        $table->unsignedSmallInteger('sort_order')->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('categories');
}
```

**`create_menus_table`**

```php
public function up(): void
{
    Schema::create('menus', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained()->restrictOnDelete();
        $table->string('name', 120);
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->string('photo')->nullable();
        $table->boolean('is_available')->default(true);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('menus');
}
```

**`create_dining_tables_table`**

```php
public function up(): void
{
    Schema::create('dining_tables', function (Blueprint $table) {
        $table->id();
        $table->string('name', 30)->unique();
        $table->string('qr_token', 64)->unique();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('dining_tables');
}
```

**`create_orders_table`**

```php
public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('order_code', 20)->unique();
        $table->foreignId('dining_table_id')->nullable()
              ->constrained('dining_tables')->nullOnDelete();
        $table->foreignId('processed_by')->nullable()
              ->constrained('users')->nullOnDelete();
        $table->enum('source', ['qr', 'kasir']);
        $table->enum('status', ['menunggu', 'diproses', 'selesai', 'dibatalkan'])
              ->default('menunggu');
        $table->string('customer_name', 80)->nullable();
        $table->decimal('total_amount', 12, 2)->default(0);
        $table->string('note')->nullable();
        $table->uuid('client_uuid')->nullable()->unique();
        $table->dateTime('ordered_at');
        $table->timestamps();

        $table->index('status');
        $table->index('ordered_at');
    });
}

public function down(): void
{
    Schema::dropIfExists('orders');
}
```

**`create_order_items_table`**

```php
public function up(): void
{
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->cascadeOnDelete();
        $table->foreignId('menu_id')->constrained()->restrictOnDelete();
        $table->string('menu_name', 120);
        $table->decimal('unit_price', 10, 2);
        $table->unsignedSmallInteger('quantity');
        $table->decimal('subtotal', 12, 2);
        $table->string('note')->nullable();
    });
}

public function down(): void
{
    Schema::dropIfExists('order_items');
}
```

**`create_payments_table`**

```php
public function up(): void
{
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->unique()->constrained()->restrictOnDelete();
        $table->foreignId('user_id')->constrained()->restrictOnDelete();
        $table->enum('method', ['tunai', 'qris', 'transfer']);
        $table->decimal('amount_paid', 12, 2);
        $table->decimal('change_amount', 12, 2)->default(0);
        $table->dateTime('paid_at');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('payments');
}
```

**`create_ingredients_table`**

```php
public function up(): void
{
    Schema::create('ingredients', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100);
        $table->string('unit', 20);
        $table->decimal('stock', 12, 2)->default(0);
        $table->decimal('min_stock', 12, 2)->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('ingredients');
}
```

**`create_menu_ingredients_table`**

```php
public function up(): void
{
    Schema::create('menu_ingredients', function (Blueprint $table) {
        $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
        $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
        $table->decimal('qty_used', 10, 2);
        $table->primary(['menu_id', 'ingredient_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('menu_ingredients');
}
```

**`create_stock_movements_table`**

```php
public function up(): void
{
    Schema::create('stock_movements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ingredient_id')->constrained()->restrictOnDelete();
        $table->foreignId('user_id')->constrained()->restrictOnDelete();
        $table->enum('type', ['masuk', 'keluar', 'penyesuaian']);
        $table->decimal('quantity', 12, 2);
        $table->string('note')->nullable();
        $table->timestamp('created_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('stock_movements');
}
```

**`create_expenses_table`**

```php
public function up(): void
{
    Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->restrictOnDelete();
        $table->string('description', 150);
        $table->decimal('amount', 12, 2);
        $table->date('expense_date')->index();
        $table->string('note')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('expenses');
}
```

### 4.4 Menjalankan migration

Pastikan **urutan timestamp pada nama file** sesuai urutan pembuatan di atas (kategori sebelum menu, orders sebelum order_items, dan seterusnya), lalu:

```bash
php artisan migrate
```

Jika ingin mengulang dari awal (data akan terhapus):

```bash
php artisan migrate:fresh
```

---

## 5. Seeder Akun Awal (Laravel)

```bash
php artisan make:seeder UserSeeder
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

public function run(): void
{
    User::create([
        'name' => 'Muhammad Fikri Firdaus',
        'email' => 'admin@debarrio.id',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'is_active' => true,
    ]);

    User::create([
        'name' => 'Kasir Warkop',
        'email' => 'kasir@debarrio.id',
        'password' => Hash::make('password'),
        'role' => 'kasir',
        'is_active' => true,
    ]);
}
```

Jalankan:

```bash
php artisan db:seed --class=UserSeeder
```

> Ganti `password` dengan kata sandi yang kuat sebelum aplikasi dipakai sungguhan.

---

## 6. Verifikasi

```sql
USE warkop_de_barrio;

SHOW TABLES;                       -- harus tampil 11 tabel (+ tabel bawaan Laravel bila Cara B)
DESCRIBE orders;
SHOW CREATE TABLE payments;

-- Cek foreign key
SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'warkop_de_barrio' AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME;
```

Tabel yang seharusnya muncul:

`users`, `categories`, `menus`, `dining_tables`, `orders`, `order_items`, `payments`, `ingredients`, `menu_ingredients`, `stock_movements`, `expenses`.

---

## 7. Catatan Rancangan

- **Salinan nama dan harga:** `order_items.menu_name` dan `order_items.unit_price` menyimpan nilai saat transaksi, sehingga perubahan harga menu tidak mengubah riwayat.
- **Sinkronisasi offline:** `orders.client_uuid` (UNIQUE, boleh NULL) dipakai agar pesanan dari kasir offline tidak tersimpan ganda saat sinkronisasi.
- **Aturan hapus:** `RESTRICT` dipakai pada data transaksi dan keuangan agar riwayat tidak hilang. Pengguna atau menu yang tidak dipakai cukup dinonaktifkan (`is_active` atau `is_available`).
- **Laporan** (harian, bulanan, menu terlaris) tidak punya tabel sendiri. Datanya diambil lewat query dari `orders`, `order_items`, `payments`, dan `expenses`.
- **`menu_ingredients` opsional:** bila takaran bahan per menu tidak dicatat, stok cukup dikelola manual lewat `stock_movements`.
- **Metode pembayaran** (`tunai`, `qris`, `transfer`) adalah contoh; sesuaikan dengan kondisi warkop.
- **Tabel bawaan Laravel** (`migrations`, `sessions`, `cache`, `jobs`, `password_reset_tokens`) tidak termasuk dalam 11 tabel di atas.

---

## 8. Tambahan Opsional: Akun Pelanggan

Laporan Progres Minggu 3 hanya memiliki peran `admin` dan `kasir`. Jika desain Figma dengan **akun pelanggan opsional** (daftar, login, riwayat pesanan) dipakai, tambahkan perubahan berikut. Bagian ini **belum ada di laporan**, jadi perbarui laporannya bila dipakai.

```sql
ALTER TABLE users
  MODIFY role ENUM('admin','kasir','pelanggan') NOT NULL DEFAULT 'pelanggan';

ALTER TABLE orders
  ADD COLUMN user_id BIGINT UNSIGNED NULL AFTER processed_by,
  ADD CONSTRAINT orders_user_id_foreign FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE SET NULL;
```

Padanan di Laravel:

```php
// pada migration users
$table->enum('role', ['admin', 'kasir', 'pelanggan'])->default('pelanggan');

// pada migration orders
$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
```

`orders.user_id` bernilai NULL untuk pesanan tanpa login (misalnya pesanan QR sebagai tamu).
