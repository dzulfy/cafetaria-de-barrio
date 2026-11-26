<?php

namespace Database\Seeders;

use App\Models\Pengeluaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan User Admin (Owner) dan Staf Ayu Lestari tersedia
        $owner = User::firstOrCreate(
            ['email' => 'admin@debarrio.id'],
            [
                'nama' => 'Muhammad Fikri Firdaus',
                'password' => Hash::make('password'),
                'peran' => 'admin',
                'status_aktif' => true,
            ]
        );

        $ayu = User::firstOrCreate(
            ['email' => 'ayu@debarrio.id'],
            [
                'nama' => 'Ayu Lestari',
                'password' => Hash::make('password'),
                'peran' => 'kasir',
                'status_aktif' => true,
            ]
        );

        $ownerId = $owner->id;
        $ayuId = $ayu->id;

        // Bersihkan tabel pengeluaran agar data fresh dan presisi
        Pengeluaran::truncate();

        // 2. Data 21 Pengeluaran (Total = Rp 6.120.000, Hari Ini = Rp 320.000)
        // 6 data pertama persis sesuai halaman 1 desain acuan
        $today = now()->toDateString();
        $kemarin = now()->subDay()->toDateString();
        $duaHariLalu = now()->subDays(2)->toDateString();
        $empatHariLalu = now()->subDays(4)->toDateString();

        $items = [
            // Halaman 1 (Item 1-6) - Persis Desain Acuan
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Susu UHT dan gula aren',
                'jumlah' => 245000,
                'kategori' => 'Bahan baku',
                'tanggal_pengeluaran' => $today, // Sesuai tanggal hari ini / 18 Nov
                'catatan' => 'Restock bahan minuman',
                'created_at' => now()->subHours(2),
            ],
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Es batu',
                'jumlah' => 75000,
                'kategori' => 'Bahan baku',
                'tanggal_pengeluaran' => $today,
                'catatan' => 'Es kristal 5 kantong',
                'created_at' => now()->subHours(3),
            ],
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Biji kopi arabica 1 kg',
                'jumlah' => 150000,
                'kategori' => 'Bahan baku',
                'tanggal_pengeluaran' => $kemarin,
                'catatan' => 'Arabica Gayo roast medium',
                'created_at' => now()->subDay()->subHours(4),
            ],
            [
                'pengguna_id' => $ayuId,
                'deskripsi' => 'Pisang dan telur',
                'jumlah' => 110000,
                'kategori' => 'Bahan baku',
                'tanggal_pengeluaran' => $duaHariLalu,
                'catatan' => 'Pisang kepok & telur ayam 2 kg',
                'created_at' => now()->subDays(2)->subHours(5),
            ],
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Token listrik',
                'jumlah' => 300000,
                'kategori' => 'Operasional',
                'tanggal_pengeluaran' => $duaHariLalu,
                'catatan' => 'PLN prabayar kafe',
                'created_at' => now()->subDays(2)->subHours(6),
            ],
            [
                'pengguna_id' => $ayuId,
                'deskripsi' => 'Gas LPG 3 kg',
                'jumlah' => 95000,
                'kategori' => 'Operasional',
                'tanggal_pengeluaran' => $empatHariLalu,
                'catatan' => 'Isi ulang 4 tabung',
                'created_at' => now()->subDays(4)->subHours(3),
            ],

            // Halaman 2 & 3 (Item 7-21) - Total sisa = Rp 5.145.000 agar Grand Total = Rp 6.120.000
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Beli cup plastik 16oz & sedotan',
                'jumlah' => 350000,
                'kategori' => 'Operasional',
                'tanggal_pengeluaran' => now()->subDays(5)->toDateString(),
                'catatan' => '1 dus cup & sedotan kertas',
                'created_at' => now()->subDays(5),
            ],
            [
                'pengguna_id' => $ayuId,
                'deskripsi' => 'Sirup vanila & karamel',
                'jumlah' => 280000,
                'kategori' => 'Bahan baku',
                'tanggal_pengeluaran' => now()->subDays(6)->toDateString(),
                'catatan' => 'Sirup perisa minuman',
                'created_at' => now()->subDays(6),
            ],
            [
                'pengguna_id' => $ayuId,
                'deskripsi' => 'Galon air mineral (10 galon)',
                'jumlah' => 200000,
                'kategori' => 'Bahan baku',
                'tanggal_pengeluaran' => now()->subDays(7)->toDateString(),
                'catatan' => 'Air seduh & masak',
                'created_at' => now()->subDays(7),
            ],
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Maintenance grinder kopi',
                'jumlah' => 450000,
                'kategori' => 'Operasional',
                'tanggal_pengeluaran' => now()->subDays(8)->toDateString(),
                'catatan' => 'Servis burr grinder',
                'created_at' => now()->subDays(8),
            ],
            [
                'pengguna_id' => $ayuId,
                'deskripsi' => 'Krimer kental manis & cokelat bubuk',
                'jumlah' => 320000,
                'kategori' => 'Bahan baku',
                'tanggal_pengeluaran' => now()->subDays(9)->toDateString(),
                'catatan' => 'Bahan dasar menu non-coffee',
                'created_at' => now()->subDays(9),
            ],
            [
                'pengguna_id' => $ayuId,
                'deskripsi' => 'Kantong kresek & paper bag',
                'jumlah' => 120000,
                'kategori' => 'Operasional',
                'tanggal_pengeluaran' => now()->subDays(10)->toDateString(),
                'catatan' => 'Kemasan take-away',
                'created_at' => now()->subDays(10),
            ],
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Biji kopi robusta 5 kg',
                'jumlah' => 450000,
                'kategori' => 'Bahan baku',
                'tanggal_pengeluaran' => now()->subDays(11)->toDateString(),
                'catatan' => 'House blend base',
                'created_at' => now()->subDays(11),
            ],
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Wi-Fi bulanan & internet',
                'jumlah' => 400000,
                'kategori' => 'Operasional',
                'tanggal_pengeluaran' => now()->subDays(12)->toDateString(),
                'catatan' => 'IndiHome 100 Mbps',
                'created_at' => now()->subDays(12),
            ],
            [
                'pengguna_id' => $ayuId,
                'deskripsi' => 'Sabun cuci piring & spons',
                'jumlah' => 65000,
                'kategori' => 'Operasional',
                'tanggal_pengeluaran' => now()->subDays(13)->toDateString(),
                'catatan' => 'Perlengkapan bar',
                'created_at' => now()->subDays(13),
            ],
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Susu UHT 2 karton',
                'jumlah' => 410000,
                'kategori' => 'Bahan baku',
                'tanggal_pengeluaran' => now()->subDays(14)->toDateString(),
                'catatan' => 'Diamond full cream 24 liter',
                'created_at' => now()->subDays(14),
            ],
            [
                'pengguna_id' => $ayuId,
                'deskripsi' => 'Tissue meja & sedotan boba',
                'jumlah' => 150000,
                'kategori' => 'Operasional',
                'tanggal_pengeluaran' => now()->subDays(15)->toDateString(),
                'catatan' => 'Restock perlengkapan meja',
                'created_at' => now()->subDays(15),
            ],
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Gula pasir & gula aren cair',
                'jumlah' => 250000,
                'kategori' => 'Bahan baku',
                'tanggal_pengeluaran' => now()->subDays(16)->toDateString(),
                'catatan' => 'Pemanis minuman',
                'created_at' => now()->subDays(16),
            ],
            [
                'pengguna_id' => $ayuId,
                'deskripsi' => 'Beli cetakan es batu & lap microfiber',
                'jumlah' => 100000,
                'kategori' => 'Operasional',
                'tanggal_pengeluaran' => now()->subDays(17)->toDateString(),
                'catatan' => 'Peralatan barista',
                'created_at' => now()->subDays(17),
            ],
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Teh celup & serbuk matcha',
                'jumlah' => 300000,
                'kategori' => 'Bahan baku',
                'tanggal_pengeluaran' => now()->subDays(18)->toDateString(),
                'catatan' => 'Menu artisan tea & matcha',
                'created_at' => now()->subDays(18),
            ],
            [
                'pengguna_id' => $ownerId,
                'deskripsi' => 'Biaya kebersihan & retribusi sampah',
                'jumlah' => 1300000,
                'kategori' => 'Operasional',
                'tanggal_pengeluaran' => now()->subDays(19)->toDateString(),
                'catatan' => 'Iuran lingkungan bulanan',
                'created_at' => now()->subDays(19),
            ],
        ];

        foreach ($items as $item) {
            Pengeluaran::create($item);
        }
    }
}
