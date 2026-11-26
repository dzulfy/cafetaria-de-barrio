<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pengeluaran;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin?->id ?? 1;

        // 1. Pastikan Meja 01 s/d Meja 06 tersedia
        $mejas = ['Meja 01', 'Meja 02', 'Meja 03', 'Meja 04', 'Meja 05', 'Meja 06'];
        foreach ($mejas as $m) {
            Meja::firstOrCreate(
                ['nomor_meja' => $m],
                ['token_qr' => Str::random(32), 'status_aktif' => true]
            );
        }

        $meja05 = Meja::where('nomor_meja', 'Meja 05')->first();
        $meja02 = Meja::where('nomor_meja', 'Meja 02')->first();

        // 2. Pastikan Menu terlaris tersedia
        $kategoriKopi = Kategori::firstOrCreate(['nama' => 'Kopi'], ['urutan' => 1]);
        $kategoriSnack = Kategori::firstOrCreate(['nama' => 'Makanan Ringan'], ['urutan' => 3]);

        $menuList = [
            ['nama' => 'Kopi Susu Gula Aren', 'kategori_id' => $kategoriKopi->id, 'harga' => 20000],
            ['nama' => 'Es Kopi Susu', 'kategori_id' => $kategoriKopi->id, 'harga' => 18000],
            ['nama' => 'Pisang Goreng', 'kategori_id' => $kategoriSnack->id, 'harga' => 15000],
            ['nama' => 'V60 Manual Brew', 'kategori_id' => $kategoriKopi->id, 'harga' => 22000],
        ];

        foreach ($menuList as $m) {
            DB::table('menu')->updateOrInsert(
                ['nama' => $m['nama']],
                [
                    'kategori_id' => $m['kategori_id'],
                    'deskripsi' => 'Menu andalan De Barrio',
                    'harga' => $m['harga'],
                    'status_tersedia' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 3. Seed Pengeluaran hari ini (3 transaksi = Rp 320.000)
        $today = now()->toDateString();
        Pengeluaran::whereDate('tanggal_pengeluaran', $today)->delete();

        $pengeluarans = [
            ['deskripsi' => 'Beli cup take-away & sedotan', 'jumlah' => 150000],
            ['deskripsi' => 'Belanja sayur & bumbu dapur', 'jumlah' => 100000],
            ['deskripsi' => 'Beli gas elpiji 3kg & es batu', 'jumlah' => 70000],
        ];

        foreach ($pengeluarans as $p) {
            Pengeluaran::create([
                'pengguna_id' => $adminId,
                'deskripsi' => $p['deskripsi'],
                'jumlah' => $p['jumlah'],
                'tanggal_pengeluaran' => $today,
                'catatan' => 'Operasional harian',
            ]);
        }

        // 4. Seed Pesanan Hari Ini (Total 47 pesanan, 8 menunggu, total Rp 1.248.000)
        Pesanan::whereDate('waktu_pesan', $today)->delete();

        // Buat 38 pesanan selesai lampau terlebih dahulu:
        for ($i = 1; $i <= 36; $i++) {
            Pesanan::create([
                'kode_pesanan' => sprintf('DB-0%03d', 70 + $i),
                'meja_id' => null,
                'pengguna_id' => $adminId,
                'sumber' => 'kasir',
                'status' => 'selesai',
                'nama_pelanggan' => 'Pelanggan Selesai '.$i,
                'total_harga' => 26000,
                'waktu_pesan' => now()->subHours(4)->subMinutes($i * 5),
            ]);
        }

        Pesanan::create([
            'kode_pesanan' => 'DB-0107',
            'meja_id' => null,
            'pengguna_id' => $adminId,
            'sumber' => 'kasir',
            'status' => 'selesai',
            'nama_pelanggan' => 'Pelanggan Selesai 37',
            'total_harga' => 32000,
            'waktu_pesan' => now()->subHours(3)->subMinutes(30),
        ]);

        Pesanan::create([
            'kode_pesanan' => 'DB-0108',
            'meja_id' => null,
            'pengguna_id' => $adminId,
            'sumber' => 'kasir',
            'status' => 'selesai',
            'nama_pelanggan' => 'Pelanggan Selesai 38',
            'total_harga' => 30000,
            'waktu_pesan' => now()->subHours(3),
        ]);

        // 6 pesanan menunggu sebelumnya (total status menunggu = 8):
        for ($i = 1; $i <= 6; $i++) {
            Pesanan::create([
                'kode_pesanan' => sprintf('DB-0%03d', 114 + $i),
                'meja_id' => $meja02?->id,
                'pengguna_id' => $adminId,
                'sumber' => 'qr',
                'status' => 'menunggu',
                'nama_pelanggan' => 'Pelanggan Menunggu '.$i,
                'total_harga' => 26000,
                'waktu_pesan' => now()->subHours(1)->subMinutes($i * 5),
            ]);
        }

        // 3 Pesanan Terbaru yang tampil di card Pesanan Terbaru (dibuat paling akhir / paling baru):
        Pesanan::create([
            'kode_pesanan' => 'DB-0121',
            'meja_id' => null,
            'pengguna_id' => $adminId,
            'sumber' => 'kasir',
            'status' => 'selesai',
            'nama_pelanggan' => 'Takeaway Kasir',
            'total_harga' => 24000,
            'waktu_pesan' => now()->subMinutes(45),
        ]);

        Pesanan::create([
            'kode_pesanan' => 'DB-0123',
            'meja_id' => $meja02?->id,
            'pengguna_id' => $adminId,
            'sumber' => 'qr',
            'status' => 'menunggu',
            'nama_pelanggan' => 'Pelanggan Meja 2',
            'total_harga' => 28000,
            'waktu_pesan' => now()->subMinutes(25),
        ]);

        Pesanan::create([
            'kode_pesanan' => 'DB-0124',
            'meja_id' => $meja05?->id,
            'pengguna_id' => $adminId,
            'sumber' => 'qr',
            'status' => 'menunggu',
            'nama_pelanggan' => 'Pelanggan Meja 5',
            'total_harga' => 42000,
            'waktu_pesan' => now()->subMinutes(10),
        ]);
    }
}
