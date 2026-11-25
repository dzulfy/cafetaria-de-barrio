# Catatan Perubahan & Progres Pengerjaan Fitur Stok Bahan
**Proyek**: Cafetaria De Barrio  
**Branch**: `feature/inventory-management`  
**Fitur**: Manajemen Stok Bahan & Mutasi Stok (Inventory Management)  
**Pengembang**: M. Fikri Firdaus (Admin / Owner)  

---

## 📌 Ringkasan Pengerjaan
Dokumen ini mencatat seluruh rangkaian perubahan, penambahan file, konfigurasi database, integrasi antarmuka (UI), dan pengujian dari awal sesi pengerjaan fitur **Stok Bahan** agar siap di-*commit* dan di-*push* ke repositori GitHub tim.

Tujuan utama fitur ini adalah mengimplementasikan antarmuka serta sistem backend pemantauan stok bahan baku kafe yang persis menyerupai desain acuan, lengkap dengan indikator level stok, status peringatan stok menipis, pencarian, filter, paginasi, operasi CRUD, dan pencatatan mutasi stok.

---

## 🚀 Rincian Progres & Perubahan (Changelog)

### 1. Sinkronisasi Branch & Dependensi
- **Merge dari `origin/develop`**: Mengintegrasikan skema basis data awal dan model Eloquent yang dibuat tim (`Bahan`, `MutasiStok`, `User`/`Pengguna`, `Menu`, `Meja`, `Pesanan`, dll.).
- **Instalasi Laravel Boost**: Menambahkan tool optimasi agent dan pedoman pengembangan Laravel.

### 2. Konfigurasi Database & Data Seeder
- **Koneksi Database `.env`**:
  - Mengonfigurasi koneksi MySQL Laragon (`127.0.0.1:3306`) dengan nama database `warkop_de_barrio`.
  - Mengaktifkan ekstensi `pdo_sqlite` dan `sqlite3` pada PHP agar pengujian otomatis (*Automated Testing*) dapat dijalankan secara cepat via *in-memory SQLite*.
- **Migrasi Tabel**: Memastikan tabel `bahan` dan `mutasi_stok` termigrasi dengan foreign key yang valid ke tabel `pengguna`.
- **Pembuatan Seeder ([database/seeders/BahanSeeder.php](database/seeders/BahanSeeder.php))**:
  - Menambahkan **18 item bahan baku** kafe lengkap dengan satuan, stok awal, dan batas minimum.
  - Halaman 1 (7 bahan pertama) dibuat **persis sesuai desain acuan**:
    1. `Biji kopi arabica` (1.200 gram, min 2.000) -> Status: **Menipis**
    2. `Susu UHT` (8.500 ml, min 5.000) -> Status: **Aman**
    3. `Gula aren` (650 gram, min 1.000) -> Status: **Menipis**
    4. `Teh celup` (140 pcs, min 50) -> Status: **Aman**
    5. `Pisang` (12 pcs, min 20) -> Status: **Menipis**
    6. `Mi instan` (60 pcs, min 30) -> Status: **Aman**
    7. `Telur` (45 butir, min 30) -> Status: **Aman**
  - Mendaftarkan `BahanSeeder::class` pada [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php).

### 3. Backend & Logika Bisnis Controller
- **Pembuatan [app/Http/Controllers/BahanController.php](app/Http/Controllers/BahanController.php)**:
  - `index()`: Menampilkan data bahan dengan paginasi 7 data per halaman, fitur pencarian nama bahan (`search`), filter status (`aman`/`menipis`), daftar peringatan stok menipis, dan opsi bahan untuk modal mutasi.
  - `store()`: Validasi dan penambahan bahan baru, sekaligus otomatis mencatat entri riwayat stok masuk awal ke tabel `mutasi_stok`.
  - `update()`: Pembaruan data bahan (nama, satuan, stok, batas minimum) dan otomatis mencatat mutasi `penyesuaian` bila stok diubah secara manual.
  - `destroy()`: Menghapus data bahan beserta riwayat mutasi terkait.
  - `mutasi()`: Logika pencatatan mutasi stok:
    - `masuk`: Menambah stok bahan (`+`).
    - `keluar`: Mengurangi stok bahan (`-`) dengan proteksi stok minimal 0.
    - `penyesuaian`: Menyesuaikan total stok fisik riil.
    - Riwayat dicatat ke tabel `mutasi_stok` lengkap dengan catatan dan pengguna yang mencatat.
  - `getPenggunaId()`: Proteksi foreign key agar selalu menggunakan ID pengguna yang valid saat mutasi dicatat.

### 4. Tampilan Antarmuka (UI/UX)
- **Pembuatan Layout Utama ([resources/views/layouts/admin.blade.php](resources/views/layouts/admin.blade.php))**:
  - Sidebar bernuansa *deep forest green* (`#184233`) dengan branding **De Barrio** dan logo cangkir kopi.
  - Menu navigasi: *Dashboard*, *Menu & Kategori*, **Stok Bahan** (*Active State* pill `#285744`), *Pengeluaran*, *Laporan*, *Pengguna*, *Meja & QR*.
  - Kartu profil di sidebar bawah: Avatar **MF**, nama **M. Fikri Firdaus**, jabatan **Admin / Owner**, dan tombol logout.
  - Dukungan build Tailwind CSS v4 serta CDN *fallback* untuk kompatibilitas lintas environment.
- **Halaman Utama Stok Bahan ([resources/views/bahan/index.blade.php](resources/views/bahan/index.blade.php))**:
  - **Header**: Judul *"Stok Bahan"*, deskripsi *"Pantau stok dan peringatan stok menipis"*, tombol *"Catat Mutasi"*, dan tombol *"+ Tambah Bahan"*.
  - **Banner Peringatan Dinamis**: Kotak peringatan warna kuning-oranye pastel dengan teks:  
    `3 bahan berada di bawah batas minimum: Biji kopi arabica, Gula aren, Pisang.`
  - **Toolbar Pencarian & Filter**: Form input pencarian *"Cari bahan..."* dan dropdown filter status *"Semua status"*, *"Aman"*, *"Menipis"*.
  - **Tabel Data Bahan**:
    - Header berlatar hijau sage lembut (`#EBF2EE`).
    - Kolom `Level`: Progress bar kapsul proporsional (warna *amber* untuk stok menipis, warna *hijau tua* untuk stok aman).
    - Kolom `Status`: Badge pill cantik (`Menipis` warna kuning-oranye, `Aman` warna mint-hijau).
    - Kolom `Aksi`: Ikon pensil untuk Edit dan ikon tempat sampah merah untuk Hapus.
    - Format angka menggunakan titik ribuan khas Indonesia (`1.200`, `8.500`, dll.).
  - **Paginasi Kustom**: Teks keterangan *"Menampilkan 1-7 dari 18 bahan"* dan kontrol tombol `< 1 2 3 >`.
  - **Modal Interaktif (Vanilla JS)**:
    - Modal Tambah Bahan Baru
    - Modal Edit Data Bahan
    - Modal Catat Mutasi Stok (Pilihan jenis: Masuk, Keluar, Penyesuaian)
    - Modal Konfirmasi Hapus Bahan

### 5. Routing ([routes/web.php](routes/web.php))
- Menambahkan route:
  - `GET /` -> diarahkan langsung ke halaman Stok Bahan (`BahanController@index`).
  - `GET /bahan` -> `bahan.index`
  - `POST /bahan` -> `bahan.store`
  - `POST /bahan/mutasi` -> `bahan.mutasi`
  - `PUT /bahan/{bahan}` -> `bahan.update`
  - `DELETE /bahan/{bahan}` -> `bahan.destroy`
  - `Redirect /stok` & `/stok-bahan` -> `/bahan` (mencegah error 404 jika ada pengetikan URL manual).

### 6. Perbaikan Bug (*Bug Fixes*) & Optimasi
- **Masalah Vite Dev Server**: Menghapus file `public/hot` yang sempat terbentuk agar browser tidak mencari port dev `:5173`, serta menjalankan `npm run build` sehingga aset statis ter-generate sempurna di `public/build/`.
- **Tailwind CSS v4 Content Path**: Menambahkan directive `@source '../views'` di `resources/css/app.css` agar styling pada file Blade terdeteksi dan di-*compile* secara utuh.
- **Dynamic Action URL**: Mengganti URL hardcoded pada form modal dengan `{{ url('bahan') }}` agar bebas kendala saat diakses via subfolder Apache/Laragon.

### 7. Pengujian (*Testing*)
- **Pembuatan [tests/Feature/BahanTest.php](tests/Feature/BahanTest.php)**:
  - `test_halaman_stok_bahan_dapat_diakses`: Verifikasi render halaman, data bahan, tombol aksi, dan banner peringatan.
  - `test_dapat_menambahkan_bahan_baru`: Verifikasi insert database bahan & mutasi awal.
  - `test_dapat_mengubah_bahan`: Verifikasi update data bahan & mutasi penyesuaian.
  - `test_dapat_mencatat_mutasi_masuk`: Verifikasi penambahan stok fisik.
  - `test_dapat_mencatat_mutasi_keluar`: Verifikasi pengurangan stok fisik.
  - `test_dapat_menghapus_bahan`: Verifikasi soft/hard delete data bahan.
  - `test_filter_dan_pencarian_bekerja`: Verifikasi query search dan filter status.
- **Hasil Test**: **9 Passed, 0 Failed** (Semua pengujian PHPUnit lulus 100%).

---

## 📂 Ringkasan File Baru & Modifikasi

| Status | File Path | Keterangan |
| :--- | :--- | :--- |
| **Baru** | `app/Http/Controllers/BahanController.php` | Controller utama manajemen stok bahan & mutasi |
| **Baru** | `resources/views/layouts/admin.blade.php` | Layout dashboard admin De Barrio & sidebar navigasi |
| **Baru** | `resources/views/bahan/index.blade.php` | Halaman utama Stok Bahan & modal-modal interaktif |
| **Baru** | `database/seeders/BahanSeeder.php` | Seeder 18 item bahan baku kafe |
| **Baru** | `tests/Feature/BahanTest.php` | Unit & Feature testing untuk fitur bahan |
| **Baru** | `CATATAN_PERUBAHAN.md` | Dokumen riwayat perubahan ini |
| **Modifikasi** | `routes/web.php` | Pendaftaran routing Stok Bahan |
| **Modifikasi** | `database/seeders/DatabaseSeeder.php` | Memanggil `BahanSeeder` |
| **Modifikasi** | `resources/css/app.css` | Menambahkan `@source '../views'` untuk Tailwind v4 |
| **Modifikasi** | `tests/Feature/ExampleTest.php` | Mengaktifkan trait `RefreshDatabase` |
| **Modifikasi** | `.env` | Konfigurasi database MySQL Laragon |

---

## 💻 Panduan Menjalankan di Lokal

1. Nyalakan Laragon (Pastikan tombol **Start All** sudah diklik dan MySQL aktif).
2. Jalankan perintah migrasi & seeder (jika belum):
   ```bash
   php artisan migrate --seed
   ```
3. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
4. Buka tautan di browser:
   [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 📤 Panduan Push ke GitHub

Untuk menyimpan seluruh progres ini ke repositori GitHub pada branch `feature/inventory-management`, jalankan perintah berikut di terminal:

```bash
git add .
git commit -m "feat(inventory): implementasi lengkap halaman stok bahan, mutasi stok, dan layout admin"
git push origin feature/inventory-management
```
