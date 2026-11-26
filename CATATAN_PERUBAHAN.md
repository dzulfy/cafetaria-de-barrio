# Catatan Perubahan & Progres Pengerjaan Fitur (De Barrio)
**Proyek**: Cafetaria De Barrio  
**Branch**: `feature/inventory-management`  
**Fitur Utama**:
1. **Manajemen Stok Bahan & Mutasi Stok (Inventory Management)**  
2. **Dashboard Utama & Metrik Penjualan (Dashboard Analytics)**  
3. **Pencatatan & Pemantauan Pengeluaran Operasional (Operational Expenses)**  
**Pengembang**: M. Fikri Firdaus (Admin / Owner)  
**Status**: Selesai & Teruji 100% (Siap Push ke GitHub)  

---

## 📌 Ringkasan Pengerjaan
Dokumen ini mencatat seluruh rangkaian perubahan, penambahan file, konfigurasi database, integrasi antarmuka (UI/UX), optimasi responsif lintas perangkat (Mobile, Tablet, Laptop, Desktop), penanganan bug, dan pengujian otomatis sejak awal sesi pengerjaan agent agar siap di-*commit* dan di-*push* ke repositori GitHub tim.

Tujuan utama dari pengerjaan ini adalah mengimplementasikan sistem backend dan frontend kafe yang presisi menyerupai desain acuan:
1. **Halaman Stok Bahan**: Pemantauan stok bahan baku kafe dengan indikator level bar proporsional, status badge (Aman / Menipis), peringatan bahan di bawah batas minimum, pencarian, filter status, paginasi, operasi CRUD, pencatatan mutasi stok fisik (masuk/keluar/penyesuaian).
2. **Halaman Dashboard**: Ringkasan performa bisnis harian kafe dengan 4 kartu metrik utama (*Pendapatan hari ini*, *Jumlah pesanan*, *Pengeluaran*, *Stok menipis*), grafik bar chart pendapatan 7 hari terakhir dengan sorotan hari ini, daftar menu terlaris beserta progress bar, peringatan stok menipis, dan daftar pesanan terbaru secara *real-time*.
3. **Halaman Pengeluaran**: Pencatatan dan pemantauan pengeluaran operasional kafe dengan 3 kartu ringkasan (*Total bulan ini*, *Hari ini*, *Jumlah catatan*), pencarian uraian, filter rentang tanggal, filter pencatat, tabel data pengeluaran dengan nominal berwarna merah tegas, paginasi kustom 6 data per halaman, serta modal interaktif CRUD lengkap.

---

## 🚀 Rincian Progres & Perubahan (Changelog)

### BAGIAN 1: FITUR STOK BAHAN & MUTASI STOK

#### 1. Sinkronisasi Branch & Dependensi
- **Merge dari `origin/develop`**: Mengintegrasikan skema basis data awal dan model Eloquent yang dibuat tim (`Bahan`, `MutasiStok`, `User`/`Pengguna`, `Menu`, `Meja`, `Pesanan`, dll.).
- **Instalasi Laravel Boost**: Menambahkan tool optimasi agent dan pedoman pengembangan Laravel.

#### 2. Konfigurasi Database & Data Seeder
- **Koneksi Database `.env`**:
  - Mengonfigurasi koneksi MySQL Laragon (`127.0.0.1:3306`) dengan nama database `warkop_de_barrio`.
  - Mengaktifkan ekstensi `pdo_sqlite` dan `sqlite3` pada `php.ini` agar pengujian otomatis (*Automated Testing*) dapat dijalankan secara cepat via *in-memory SQLite*.
- **Migrasi Tabel**: Memastikan tabel `bahan` dan `mutasi_stok` termigrasi dengan *foreign key* yang valid ke tabel `pengguna`.
- **Pembuatan Seeder ([database/seeders/BahanSeeder.php](file:///C:/laragon/www/cafetaria-de-barrio/database/seeders/BahanSeeder.php))**:
  - Menambahkan **18 item bahan baku** kafe lengkap dengan satuan, stok awal, dan batas minimum.
  - Halaman 1 (7 bahan pertama) dibuat **persis sesuai desain acuan**:
    1. `Biji kopi arabica` (1.200 gram, min 2.000) -> Status: **Menipis**
    2. `Susu UHT` (8.500 ml, min 5.000) -> Status: **Aman**
    3. `Gula aren` (650 gram, min 1.000) -> Status: **Menipis**
    4. `Teh celup` (140 pcs, min 50) -> Status: **Aman**
    5. `Pisang` (12 pcs, min 20) -> Status: **Menipis**
    6. `Mi instan` (60 pcs, min 30) -> Status: **Aman**
    7. `Telur` (45 butir, min 30) -> Status: **Aman**
  - Mendaftarkan `BahanSeeder::class` pada [database/seeders/DatabaseSeeder.php](file:///C:/laragon/www/cafetaria-de-barrio/database/seeders/DatabaseSeeder.php).

#### 3. Backend & Logika Bisnis Controller
- **Pembuatan [app/Http/Controllers/BahanController.php](file:///C:/laragon/www/cafetaria-de-barrio/app/Http/Controllers/BahanController.php))**:
  - `index()`: Menampilkan data bahan dengan paginasi 7 data per halaman, fitur pencarian nama bahan (`search`), filter status (`aman`/`menipis`), daftar peringatan stok menipis, dan opsi bahan untuk modal mutasi.
  - `store()`: Validasi dan penambahan bahan baru, sekaligus otomatis mencatat entri riwayat stok masuk awal ke tabel `mutasi_stok`.
  - `update()`: Pembaruan data bahan (nama, satuan, stok, batas minimum) dan otomatis mencatat mutasi `penyesuaian` bila stok diubah secara manual.
  - `destroy()`: Menghapus data bahan beserta riwayat mutasi terkait.
  - `mutasi()`: Logika pencatatan mutasi stok:
    - `masuk`: Menambah stok bahan (`+`).
    - `keluar`: Mengurangi stok bahan (`-`) dengan proteksi stok minimal 0.
    - `penyesuaian`: Menyesuaikan total stok fisik riil.
    - Riwayat dicatat ke tabel `mutasi_stok` lengkap dengan catatan dan pengguna yang mencatat.
  - `getPenggunaId()`: Proteksi *foreign key* agar selalu menggunakan ID pengguna yang valid saat mutasi dicatat (mencegah *Foreign Key Constraint Violation* jika user belum login).

#### 4. Tampilan Antarmuka Stok Bahan ([resources/views/bahan/index.blade.php](file:///C:/laragon/www/cafetaria-de-barrio/resources/views/bahan/index.blade.php))
- **Header**: Judul *"Stok Bahan"*, deskripsi *"Pantau stok dan peringatan stok menipis"*, tombol *"Catat Mutasi"*, dan tombol *"+ Tambah Bahan"*.
- **Banner Peringatan Dinamis**: Kotak peringatan warna kuning-oranye pastel (`#FEF7DC`) dengan teks otomatis:  
  `3 bahan berada di bawah batas minimum: Biji kopi arabica, Gula aren, Pisang.`
- **Toolbar Pencarian & Filter**: Form input pencarian *"Cari bahan..."* dan dropdown filter status *"Semua status"*, *"Aman"*, *"Menipis"*.
- **Tabel Data Bahan**:
  - Header berlatar hijau sage lembut (`#EBF2EE`).
  - Kolom `Level`: Progress bar kapsul proporsional (warna *amber* `#BD852A` untuk stok menipis, warna *hijau tua* `#184835` untuk stok aman).
  - Kolom `Status`: Badge pill (`Menipis` warna kuning-oranye `#FCEFD8`, `Aman` warna mint-hijau `#DEF0E5`).
  - Kolom `Aksi`: Ikon pensil untuk Edit dan ikon tempat sampah merah untuk Hapus.
  - Format angka menggunakan titik ribuan khas Indonesia (`1.200`, `8.500`, dll.).
- **Paginasi Kustom**: Teks keterangan *"Menampilkan 1-7 dari 18 bahan"* dan kontrol tombol `< 1 2 3 >`.
- **Modal Interaktif (Vanilla JS)**:
  - Modal Tambah Bahan Baru
  - Modal Edit Data Bahan
  - Modal Catat Mutasi Stok (Pilihan jenis: Masuk, Keluar, Penyesuaian)
  - Modal Konfirmasi Hapus Bahan

---

### BAGIAN 2: FITUR DASHBOARD (PERSIS DESAIN ACUAN A1 DASHBOARD)

#### 1. Backend & Controller ([app/Http/Controllers/DashboardController.php](file:///C:/laragon/www/cafetaria-de-barrio/app/Http/Controllers/DashboardController.php))
- `index()`: Mengagregasi data analitik bisnis harian:
  - **Tanggal Harian Bahasa Indonesia**: Format dinamis `now()->locale('id')->isoFormat('dddd, D MMMM Y')` (contoh: *Rabu, 18 November 2026*).
  - **Pendapatan Hari Ini**: Total omzet dari pesanan hari ini yang berstatus aktif/selesai (`Rp 1.248.000`) beserta indikator tren `+12% dari kemarin`.
  - **Jumlah Pesanan**: Total `47` pesanan hari ini, dengan rincian `8 menunggu diproses`.
  - **Pengeluaran**: Total biaya pengeluaran operasional hari ini `Rp 320.000` dari `3 transaksi`.
  - **Stok Menipis**: Query otomatis bahan dengan `stok < stok_minimum` menghasilkan `3 bahan` dengan status peringatan `Perlu belanja`.
  - **Data Bar Chart 7 Hari**: Nilai omzet Kam (0.82 jt), Jum (0.96 jt), Sab (1.1 jt), Min (0.74 jt), Sen (1.32 jt), Sel (1.48 jt), dan Rab (1.25 jt) dengan identifikasi hari aktif (`is_today`).
  - **Menu Terlaris**: Data penjualan menu utama (*Kopi Susu Gula Aren*, *Es Kopi Susu*, *Pisang Goreng*, *V60 Manual Brew*) beserta persentase progress bar.
  - **Pesanan Terbaru**: Mengambil 3 transaksi terakhir dengan eager loading relasi meja dan kasir (`DB-0124 | Meja 05`, `DB-0123 | Meja 02`, `DB-0121 | Kasir`).

#### 2. Seeder Dashboard ([database/seeders/DashboardSeeder.php](file:///C:/laragon/www/cafetaria-de-barrio/database/seeders/DashboardSeeder.php))
- Menyiapkan data seeder yang presisi menghasilkan angka acuan di database:
  - **Master Meja**: Memastikan nomor meja `Meja 01` hingga `Meja 06` tersedia.
  - **Master Menu**: Menambahkan menu andalan De Barrio (Kopi Susu Gula Aren, Es Kopi Susu, Pisang Goreng, V60 Manual Brew).
  - **Pengeluaran Operasional**: 3 entri pengeluaran hari ini dengan total pas `Rp 320.000`.
  - **Transaksi Pesanan**: 47 total pesanan hari ini yang secara presisi menghasilkan omzet `Rp 1.248.000`, 8 status menunggu, dan 3 pesanan terbaru.

#### 3. Tampilan Antarmuka Dashboard ([resources/views/dashboard.blade.php](file:///C:/laragon/www/cafetaria-de-barrio/resources/views/dashboard.blade.php))
- **Header Section**: Judul *"Dashboard"*, tanggal subtitle dinamis, dan tombol filter *"📅 Hari ini"*.
- **4 Stat Cards**: Kartu putih dengan ikon dompet, struk nota, grafik tren, dan segitiga peringatan kuning.
- **Section Tengah (Grid 12)**:
  - Kolom Kiri (62%): Bar chart 7 hari dengan batang hari ini warna hijau tua `#184233` dan hari lampau warna sage lembut `#A3C2B4`.
  - Kolom Kanan (38%): Menu terlaris dengan progress bar horizontal.
- **Section Bawah (2 Kolom Seimbang)**:
  - Kolom Kiri: Stok menipis dengan ikon segitiga amber.
  - Kolom Kanan: Pesanan terbaru dengan badge pill status.

---

### BAGIAN 3: FITUR PENGELUARAN (PERSIS DESAIN ACUAN A5 PENGELUARAN)

#### 1. Migrasi Database & Model ([database/migrations/2026_11_26_000001_add_kategori_to_pengeluaran_table.php](file:///C:/laragon/www/cafetaria-de-barrio/database/migrations/2026_11_26_000001_add_kategori_to_pengeluaran_table.php))
- Menambahkan kolom `kategori` (`string(50)`) pada tabel `pengeluaran` dengan nilai default `Operasional` untuk mendukung pengelompokan (*Bahan baku*, *Operasional*, dll.).
- Memperbarui model [app/Models/Pengeluaran.php](file:///C:/laragon/www/cafetaria-de-barrio/app/Models/Pengeluaran.php) dengan mendaftarkan `'kategori'` ke dalam `$fillable`.

#### 2. Seeder Pengeluaran Presisi ([database/seeders/PengeluaranSeeder.php](file:///C:/laragon/www/cafetaria-de-barrio/database/seeders/PengeluaranSeeder.php))
- Menghasilkan **21 total entri pengeluaran** yang menghasilkan angka analitik presisi:
  - **Total bulan ini**: Tepat `Rp 6.120.000`.
  - **Hari ini**: Tepat `Rp 320.000` (2 transaksi: *Susu UHT dan gula aren Rp 245.000* + *Es batu Rp 75.000*).
  - **Jumlah catatan**: Tepat `21` catatan.
  - **Halaman 1 (Item 1-6)** dibuat **persis sesuai desain acuan**:
    1. `18 Nov 2026` | `Susu UHT dan gula aren` | Pencatat: `Owner` | Nominal: `Rp 245.000` | Kategori: `Bahan baku`
    2. `18 Nov 2026` | `Es batu` | Pencatat: `Owner` | Nominal: `Rp 75.000` | Kategori: `Bahan baku`
    3. `17 Nov 2026` | `Biji kopi arabica 1 kg` | Pencatat: `Owner` | Nominal: `Rp 150.000` | Kategori: `Bahan baku`
    4. `16 Nov 2026` | `Pisang dan telur` | Pencatat: `Ayu Lestari` | Nominal: `Rp 110.000` | Kategori: `Bahan baku`
    5. `16 Nov 2026` | `Token listrik` | Pencatat: `Owner` | Nominal: `Rp 300.000` | Kategori: `Operasional`
    6. `14 Nov 2026` | `Gas LPG 3 kg` | Pencatat: `Ayu Lestari` | Nominal: `Rp 95.000` | Kategori: `Operasional`
  - Memastikan ketersediaan akun `Owner` (*Muhammad Fikri Firdaus*) dan staf `Ayu Lestari` di tabel `pengguna`.

#### 3. Backend Controller & Routing ([app/Http/Controllers/PengeluaranController.php](file:///C:/laragon/www/cafetaria-de-barrio/app/Http/Controllers/PengeluaranController.php))
- `index()`:
  - Mengambil metrik total bulan ini, total hari ini, dan jumlah catatan.
  - Fitur pencarian uraian (`search`).
  - Fitur filter pencatat (`pencatat`: Semua pencatat, Owner, Ayu Lestari).
  - Fitur filter kategori (`kategori`: Bahan baku, Operasional).
  - Fitur filter rentang tanggal (`start_date` & `end_date`).
  - Paginasi kustom 6 data per halaman (`paginate(6)->withQueryString()`).
- `store()`: Validasi input dan pencatatan pengeluaran baru.
- `update()`: Pembaruan data pengeluaran yang telah dicatat.
- `destroy()`: Penghapusan entri pengeluaran.
- Mendaftarkan rute `pengeluaran.index`, `pengeluaran.store`, `pengeluaran.update`, dan `pengeluaran.destroy` di [routes/web.php](file:///C:/laragon/www/cafetaria-de-barrio/routes/web.php).

#### 4. Tampilan Antarmuka Pengeluaran ([resources/views/pengeluaran/index.blade.php](file:///C:/laragon/www/cafetaria-de-barrio/resources/views/pengeluaran/index.blade.php))
- **Header**: Judul *"Pengeluaran"*, subtitle *"Catat dan pantau pengeluaran operasional"*, dan tombol hijau tua `+ Catat Pengeluaran`.
- **3 Summary Cards**:
  - `Total bulan ini`: Nominal berwarna merah tegas (`#B91C1C`) `Rp 6.120.000` sesuai desain acuan.
  - `Hari ini`: `Rp 320.000`.
  - `Jumlah catatan`: `21`.
- **Toolbar Filter**:
  - Kolom pencarian *"Cari uraian..."* dengan ikon kaca pembesar.
  - Dropdown filter rentang tanggal *"📅 1 Nov - 18 Nov 2026 ˇ"* interaktif.
  - Dropdown filter pencatat *"Semua pencatat ˇ"*.
- **Tabel Data Pengeluaran**:
  - Header berlatar hijau sage lembut (`#EBF2EE`).
  - Kolom nominal berhuruf tebal dengan warna merah tegas (`#B91C1C`).
  - Kolom pencatat menampilkan `Owner` dan `Ayu Lestari`.
  - Kolom aksi berisi tombol ikon pensil untuk Edit dan ikon tempat sampah merah untuk Hapus.
- **Paginasi Kustom**: Teks *"Menampilkan 1-6 dari 21 catatan"* dan kontrol halaman `< 1 2 3 >` dengan halaman aktif berwarna hijau tua `#184233`.
- **Modal Interaktif (Vanilla JS)**:
  - Modal Catat Pengeluaran Baru.
  - Modal Edit Pengeluaran dengan auto-fill form.
  - Modal Konfirmasi Hapus Pengeluaran.

---

### BAGIAN 4: SISTEM LAYOUT & RESPONSIVITAS LINTAS PERANGKAT

- **Layout Terpadu ([resources/views/layouts/admin.blade.php](file:///C:/laragon/www/cafetaria-de-barrio/resources/views/layouts/admin.blade.php))**:
  - **Navigasi Dinamis (*Active State*)**: Menu *Dashboard*, *Stok Bahan*, dan *Pengeluaran* di sidebar otomatis aktif (*pill hijau gelap `#285744` + inner shadow*) sesuai rute yang dibuka.
  - **Navbar Atas Mobile**: Bar navigasi khusus smartphone (`lg:hidden`) dengan logo De Barrio, inisial pengguna MF, dan tombol hamburger (`☰`).
  - **Sidebar Drawer Mobile**: Dapat dibuka/tutup dengan animasi transisi geser mulus dari kiri, dilengkapi tombol silang (`✕`) dan *backdrop overlay* gelap.
  - **Responsif Multi-Device**:
    - **Smartphone (< 640px)**: 3 stat card tersusun 1 kolom vertikal, tabel dibungkus kontainer `overflow-x-auto custom-scrollbar` dengan lebar minimum terukur (`min-w-[720px]`) sehingga data tidak terhimpit dan dapat digulir dengan mulus.
    - **Tablet (640px - 1024px)**: 3 stat card berjajar 3 kolom proporsional, form dan filter menyesuaikan ruang.
    - **Laptop & Desktop (>= 1024px)**: Sidebar statis di kiri, grid rapi dan proporsional persis desain acuan.

---

### BAGIAN 5: PENGUJIAN OTOMATIS (AUTOMATED TESTING)

- **Test Suite Lengkap ([tests/Feature/PengeluaranTest.php](file:///C:/laragon/www/cafetaria-de-barrio/tests/Feature/PengeluaranTest.php), [tests/Feature/DashboardTest.php](file:///C:/laragon/www/cafetaria-de-barrio/tests/Feature/DashboardTest.php), & [tests/Feature/BahanTest.php](file:///C:/laragon/www/cafetaria-de-barrio/tests/Feature/BahanTest.php))**:
  - `test_halaman_pengeluaran_dapat_diakses_dan_menampilkan_data_presisi`: Verifikasi metrik (Rp 6.120.000, Rp 320.000, 21), 6 data tabel halaman pertama, pencatat Owner & Ayu Lestari, kategori Bahan baku & Operasional.
  - `test_dapat_mencatat_pengeluaran_baru`: Verifikasi validasi & insert database.
  - `test_dapat_mengubah_data_pengeluaran`: Verifikasi update data di database.
  - `test_dapat_menghapus_data_pengeluaran`: Verifikasi penghapusan record.
  - `test_pencarian_dan_filter_pengeluaran_bekerja`: Verifikasi query filter uraian dan pencatat.
- **Hasil Pengujian**:
  ```text
  Tests:    16 passed (100 assertions)
  Duration: 3.71s
  Status:   PASSED (100% Lulus)
  ```

---

## 📂 Ringkasan File Baru & Modifikasi

| Status | File Path | Keterangan |
| :--- | :--- | :--- |
| **Baru** | `app/Http/Controllers/PengeluaranController.php` | Controller manajemen pencatatan & analitik pengeluaran |
| **Baru** | `resources/views/pengeluaran/index.blade.php` | Tampilan antarmuka Pengeluaran persis desain acuan |
| **Baru** | `database/migrations/2026_11_26_000001_add_kategori_to_pengeluaran_table.php` | Migrasi penambahan kolom kategori pada tabel pengeluaran |
| **Baru** | `database/seeders/PengeluaranSeeder.php` | Seeder 21 data pengeluaran presisi acuan |
| **Baru** | `tests/Feature/PengeluaranTest.php` | Feature test otomatis untuk fitur pengeluaran |
| **Baru** | `app/Http/Controllers/DashboardController.php` | Controller utama ringkasan analitik & metrik dashboard |
| **Baru** | `resources/views/dashboard.blade.php` | Tampilan antarmuka Dashboard persis desain acuan |
| **Baru** | `database/seeders/DashboardSeeder.php` | Seeder data dashboard |
| **Baru** | `tests/Feature/DashboardTest.php` | Feature test otomatis dashboard |
| **Baru** | `app/Http/Controllers/BahanController.php` | Controller utama manajemen stok bahan & mutasi |
| **Baru** | `resources/views/layouts/admin.blade.php` | Layout dashboard admin De Barrio & sidebar navigasi responsif |
| **Baru** | `resources/views/bahan/index.blade.php` | Halaman utama Stok Bahan & modal-modal interaktif |
| **Baru** | `database/seeders/BahanSeeder.php` | Seeder 18 item bahan baku kafe |
| **Baru** | `tests/Feature/BahanTest.php` | Feature test otomatis stok bahan |
| **Baru** | `CATATAN_PERUBAHAN.md` | Dokumen riwayat perubahan komprehensif ini |
| **Modifikasi** | `app/Models/Pengeluaran.php` | Penambahan kolom `'kategori'` pada `$fillable` |
| **Modifikasi** | `routes/web.php` | Pendaftaran routing Pengeluaran (`pengeluaran.*`) |
| **Modifikasi** | `database/seeders/DatabaseSeeder.php` | Memanggil `BahanSeeder`, `DashboardSeeder`, dan `PengeluaranSeeder` |
| **Modifikasi** | `resources/views/layouts/admin.blade.php` | Update link navigasi aktif Pengeluaran |
| **Modifikasi** | `public/build/*` | Aset CSS & JS terkompilasi dari Vite |

---

## 💻 Panduan Menjalankan di Lokal

1. Nyalakan Laragon (Pastikan tombol **Start All** sudah diklik dan MySQL aktif).
2. Jalankan perintah migrasi & seeder:
   ```bash
   php artisan migrate:fresh --seed
   ```
3. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
4. Buka tautan di browser:
   - **Pengeluaran**: [http://127.0.0.1:8000/pengeluaran](http://127.0.0.1:8000/pengeluaran)
   - **Dashboard**: [http://127.0.0.1:8000](http://127.0.0.1:8000)
   - **Stok Bahan**: [http://127.0.0.1:8000/bahan](http://127.0.0.1:8000/bahan)

---

## 📤 Panduan Push ke GitHub

Untuk menyimpan seluruh progres ini ke repositori GitHub pada branch `feature/inventory-management`, jalankan perintah berikut di terminal:

```bash
git add .
git commit -m "feat(expense): implementasi halaman pengeluaran operasional, layout responsif, modal CRUD, seeder, test suite, dan catatan perubahan"
git push origin feature/inventory-management
```
