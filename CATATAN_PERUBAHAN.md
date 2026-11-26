# Catatan Perubahan & Progres Pengerjaan Fitur (De Barrio)
**Proyek**: Cafetaria De Barrio  
**Branch**: `feature/inventory-management`  
**Fitur Utama**:
1. **Manajemen Stok Bahan & Mutasi Stok (Inventory Management)**  
2. **Dashboard Utama & Metrik Penjualan (Dashboard Analytics)**  
**Pengembang**: M. Fikri Firdaus (Admin / Owner)  
**Status**: Selesai & Teruji 100% (Siap Push ke GitHub)  

---

## 📌 Ringkasan Pengerjaan
Dokumen ini mencatat seluruh rangkaian perubahan, penambahan file, konfigurasi database, integrasi antarmuka (UI/UX), optimasi responsif lintas perangkat (Mobile, Tablet, Laptop, Desktop), penanganan bug, dan pengujian otomatis sejak awal sesi pengerjaan agent agar siap di-*commit* dan di-*push* ke repositori GitHub tim.

Tujuan utama dari pengerjaan ini adalah mengimplementasikan sistem backend dan frontend kafe yang presisi menyerupai desain acuan:
1. **Halaman Stok Bahan**: Pemantauan stok bahan baku kafe dengan indikator level bar proporsional, status badge (Aman / Menipis), peringatan bahan di bawah batas minimum, pencarian, filter status, paginasi, operasi CRUD, pencatatan mutasi stok fisik (masuk/keluar/penyesuaian).
2. **Halaman Dashboard**: Ringkasan performa bisnis harian kafe dengan 4 kartu metrik utama (*Pendapatan hari ini*, *Jumlah pesanan*, *Pengeluaran*, *Stok menipis*), grafik bar chart pendapatan 7 hari terakhir dengan sorotan hari ini, daftar menu terlaris beserta progress bar, peringatan stok menipis, dan daftar pesanan terbaru secara *real-time*.

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
- **Pembuatan [app/Http/Controllers/BahanController.php](file:///C:/laragon/www/cafetaria-de-barrio/app/Http/Controllers/BahanController.php)**:
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
  - **Pesanan Terbaru**: Mengambil 3 transaksi terakhir dengan eager loading relasi meja dan kasir.

#### 2. Seeder Dashboard ([database/seeders/DashboardSeeder.php](file:///C:/laragon/www/cafetaria-de-barrio/database/seeders/DashboardSeeder.php))
- Menyiapkan data seeder yang presisi menghasilkan angka acuan di database:
  - **Master Meja**: Memastikan nomor meja `Meja 01` hingga `Meja 06` tersedia.
  - **Master Menu**: Menambahkan menu andalan De Barrio (Kopi Susu Gula Aren, Es Kopi Susu, Pisang Goreng, V60 Manual Brew).
  - **Pengeluaran Operasional**: 3 entri pengeluaran hari ini dengan total pas `Rp 320.000` (Cup & sedotan Rp 150.000, Sayur & bumbu Rp 100.000, Gas & es batu Rp 70.000).
  - **Transaksi Pesanan**: 47 total pesanan hari ini yang secara presisi menghasilkan omzet `Rp 1.248.000`, 8 status menunggu, dan 3 pesanan terbaru yang tampil di antarmuka:
    1. `DB-0124 | Meja 05` — Status: `Menunggu` — `Rp 42.000`
    2. `DB-0123 | Meja 02` — Status: `Menunggu` — `Rp 28.000`
    3. `DB-0121 | Kasir` — Status: `Selesai` — `Rp 24.000`

#### 3. Tampilan Antarmuka Dashboard ([resources/views/dashboard.blade.php](file:///C:/laragon/www/cafetaria-de-barrio/resources/views/dashboard.blade.php))
- **Header Section**:
  - Judul *"Dashboard"* besar dan tebal, tanggal subtitle bahasa Indonesia dinamis.
  - Tombol filter *"📅 Hari ini"* warna putih dengan border elegan.
- **4 Stat Cards**:
  - Desain kartu putih ber-radius halus dengan border `#EBE8E0` dan bayangan lembut.
  - Ikon khusus di pojok kanan atas: Ikon Dompet untuk Pendapatan, Ikon Nota/Struk untuk Pesanan, Ikon Tren Grafik untuk Pengeluaran, dan Ikon Segitiga Peringatan kuning (`#BD852A`) untuk Stok Menipis.
- **Section Tengah (2 Kolom Asimetris / Grid 12)**:
  - **Kolom Kiri (62% desktop / col-span-7)**: *Pendapatan 7 hari terakhir* menampilkan grafik batang 7 hari (Kam s/d Rab). Batang hari-hari sebelumnya berwarna hijau sage lembut (`#A3C2B4`), dan hari ini disorot warna hijau tua (*forest green* `#184233`) dengan label nominal di atas batang dan nama hari di bawahnya.
  - **Kolom Kanan (38% desktop / col-span-5)**: *Menu terlaris* menampilkan 4 menu favorit dengan jumlah terjual dan progress bar horizontal hijau tua.
- **Section Bawah (2 Kolom Seimbang)**:
  - **Kolom Kiri**: *Stok menipis* menampilkan daftar bahan yang berada di bawah stok minimum (`Biji kopi arabica 1.200 g / min 2.000 g`, `Gula aren 650 g / min 1.000 g`, `Pisang 12 pcs / min 20 pcs`) lengkap dengan ikon segitiga peringatan amber dan tombol cepat *"Kelola Stok →"*.
  - **Kolom Kanan**: *Pesanan terbaru* menampilkan kode pesanan, meja/kasir, badge status pill (`Menunggu` warna kuning oranye, `Selesai` warna sage), dan nominal rupiah harga pesanan.

---

### BAGIAN 3: SISTEM LAYOUT & RESPONSIVITAS LINTAS PERANGKAT

- **Layout Terpadu ([resources/views/layouts/admin.blade.php](file:///C:/laragon/www/cafetaria-de-barrio/resources/views/layouts/admin.blade.php))**:
  - **Navigasi Dinamis (*Active State*)**: Menu *Dashboard* dan *Stok Bahan* otomatis aktif (pill hijau gelap `#285744` + shadow) sesuai route yang sedang diakses (`request()->routeIs(...)`).
  - **Navbar Atas Mobile**: Bar navigasi khusus smartphone (`lg:hidden`) dengan logo De Barrio, inisial pengguna MF, dan tombol hamburger (`☰`).
  - **Sidebar Drawer Mobile**: Dapat dibuka/tutup dengan animasi transisi geser mulus dari kiri, dilengkapi tombol silang (`✕`) dan *backdrop overlay* gelap.
  - **Responsif Grid**:
    - **Layar Smartphone (< 640px)**: 4 stat card tersusun 1 kolom vertikal, grafik dan kartu menu bertumpuk rapi, grafik horizontal scrollable (`overflow-x-auto`) jika layar sangat sempit (misal 320px-375px), teks tidak terpotong.
    - **Layar Tablet (640px - 1024px)**: 4 stat card otomatis menjadi 2 kolom, elemen layout menyesuaikan ruang secara proporsional.
    - **Layar Laptop & Desktop (>= 1024px)**: Sidebar kiri statis, 4 stat card berjajar 4 kolom, grid tengah asimetris 62:38, grid bawah 50:50 persis desain acuan.

---

### BAGIAN 4: ROUTING & OPTIMASI SISTEM

- **Routing ([routes/web.php](file:///C:/laragon/www/cafetaria-de-barrio/routes/web.php))**:
  - `GET /` -> diarahkan ke halaman Dashboard (`DashboardController@index`, named `dashboard`).
  - `GET /dashboard` -> `DashboardController@index`.
  - `GET /bahan` -> `BahanController@index` (`bahan.index`).
  - `POST /bahan` -> `BahanController@store`.
  - `POST /bahan/mutasi` -> `BahanController@mutasi`.
  - `PUT /bahan/{bahan}` -> `BahanController@update`.
  - `DELETE /bahan/{bahan}` -> `BahanController@destroy`.
  - `Redirect /stok` & `/stok-bahan` -> `/bahan`.
- **Aset & Build**:
  - File `public/hot` dibersihkan agar aplikasi tidak tergantung ke Vite dev server.
  - Sukses menjalankan `npm run build` sehingga seluruh CSS Tailwind v4 dan JavaScript terkompilasi optimal di folder `public/build/`.

---

### BAGIAN 5: PENGUJIAN OTOMATIS (AUTOMATED TESTING)

- **Test Suite Lengkap ([tests/Feature/DashboardTest.php](file:///C:/laragon/www/cafetaria-de-barrio/tests/Feature/DashboardTest.php) & [tests/Feature/BahanTest.php](file:///C:/laragon/www/cafetaria-de-barrio/tests/Feature/BahanTest.php))**:
  - `test_dashboard_page_can_be_rendered`: Memastikan halaman dashboard dapat diakses (status 200) dengan seluruh komponen card dan grafiknya.
  - `test_dashboard_displays_seeded_data_accurately`: Memastikan data dari seeder terhitung akurat (Rp 1.248.000, 47 pesanan, 8 menunggu, Rp 320.000 pengeluaran, 3 bahan menipis, menu terlaris, pesanan terbaru).
  - 9 pengujian komprehensif pada fitur Stok Bahan (CRUD, Mutasi masuk/keluar, Filter, Pencarian).
- **Hasil Pengujian**:
  ```text
  Tests:    11 passed (60 assertions)
  Duration: 3.32s
  Status:   PASSED (100% Lulus)
  ```

---

## 📂 Ringkasan File Baru & Modifikasi

| Status | File Path | Keterangan |
| :--- | :--- | :--- |
| **Baru** | `app/Http/Controllers/DashboardController.php` | Controller utama ringkasan analitik & metrik dashboard |
| **Baru** | `resources/views/dashboard.blade.php` | Tampilan antarmuka Dashboard persis desain acuan |
| **Baru** | `database/seeders/DashboardSeeder.php` | Seeder data dashboard (47 pesanan, 3 pengeluaran, meja, menu) |
| **Baru** | `tests/Feature/DashboardTest.php` | Pengujian fitur otomatis untuk dashboard |
| **Baru** | `app/Http/Controllers/BahanController.php` | Controller utama manajemen stok bahan & mutasi |
| **Baru** | `resources/views/layouts/admin.blade.php` | Layout dashboard admin De Barrio & sidebar navigasi responsif |
| **Baru** | `resources/views/bahan/index.blade.php` | Halaman utama Stok Bahan & modal-modal interaktif |
| **Baru** | `database/seeders/BahanSeeder.php` | Seeder 18 item bahan baku kafe |
| **Baru** | `tests/Feature/BahanTest.php` | Unit & Feature testing untuk fitur bahan |
| **Baru** | `CATATAN_PERUBAHAN.md` | Dokumen riwayat perubahan komprehensif ini |
| **Modifikasi** | `routes/web.php` | Menghubungkan route Dashboard (`/` & `/dashboard`) dan Stok Bahan |
| **Modifikasi** | `database/seeders/DatabaseSeeder.php` | Memanggil `BahanSeeder` dan `DashboardSeeder` |
| **Modifikasi** | `resources/css/app.css` | Menambahkan `@source '../views'` untuk Tailwind v4 |
| **Modifikasi** | `public/build/*` | Aset CSS & JS terkompilasi dari Vite |

---

## 💻 Panduan Menjalankan di Lokal

1. Nyalakan Laragon (Pastikan tombol **Start All** sudah diklik dan MySQL aktif).
2. Jalankan perintah migrasi & seeder (jika database ingin di-*refresh* dari awal):
   ```bash
   php artisan migrate:fresh --seed
   ```
3. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
4. Buka tautan di browser:
   - **Dashboard**: [http://127.0.0.1:8000](http://127.0.0.1:8000) atau [http://127.0.0.1:8000/dashboard](http://127.0.0.1:8000/dashboard)
   - **Stok Bahan**: [http://127.0.0.1:8000/bahan](http://127.0.0.1:8000/bahan)

---

## 📤 Panduan Push ke GitHub

Untuk menyimpan seluruh progres ini ke repositori GitHub pada branch `feature/inventory-management`, jalankan perintah berikut di terminal:

```bash
git add .
git commit -m "feat(dashboard): implementasi halaman dashboard analitik, layout responsif, seeder, test suite, dan catatan perubahan"
git push origin feature/inventory-management
```
