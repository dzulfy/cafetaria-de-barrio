<?php

namespace Database\Seeders;

use App\Models\Bahan;
use Illuminate\Database\Seeder;

class BahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // Sesuai persis dengan data tampilan di halaman 1
            [
                'nama' => 'Biji kopi arabica',
                'satuan' => 'gram',
                'stok' => 1200,
                'stok_minimum' => 2000,
            ],
            [
                'nama' => 'Susu UHT',
                'satuan' => 'ml',
                'stok' => 8500,
                'stok_minimum' => 5000,
            ],
            [
                'nama' => 'Gula aren',
                'satuan' => 'gram',
                'stok' => 650,
                'stok_minimum' => 1000,
            ],
            [
                'nama' => 'Teh celup',
                'satuan' => 'pcs',
                'stok' => 140,
                'stok_minimum' => 50,
            ],
            [
                'nama' => 'Pisang',
                'satuan' => 'pcs',
                'stok' => 12,
                'stok_minimum' => 20,
            ],
            [
                'nama' => 'Mi instan',
                'satuan' => 'pcs',
                'stok' => 60,
                'stok_minimum' => 30,
            ],
            [
                'nama' => 'Telur',
                'satuan' => 'butir',
                'stok' => 45,
                'stok_minimum' => 30,
            ],

            // Item tambahan agar total 18 bahan (3 halaman, 7 item per halaman)
            [
                'nama' => 'Biji kopi robusta',
                'satuan' => 'gram',
                'stok' => 3000,
                'stok_minimum' => 1500,
            ],
            [
                'nama' => 'Sirup vanila',
                'satuan' => 'ml',
                'stok' => 1500,
                'stok_minimum' => 500,
            ],
            [
                'nama' => 'Sirup karamel',
                'satuan' => 'ml',
                'stok' => 1200,
                'stok_minimum' => 500,
            ],
            [
                'nama' => 'Cokelat bubuk',
                'satuan' => 'gram',
                'stok' => 2500,
                'stok_minimum' => 1000,
            ],
            [
                'nama' => 'Matcha bubuk',
                'satuan' => 'gram',
                'stok' => 1000,
                'stok_minimum' => 500,
            ],
            [
                'nama' => 'Krimer kental manis',
                'satuan' => 'ml',
                'stok' => 4000,
                'stok_minimum' => 2000,
            ],
            [
                'nama' => 'Roti tawar',
                'satuan' => 'pack',
                'stok' => 25,
                'stok_minimum' => 10,
            ],
            [
                'nama' => 'Keju cheddar',
                'satuan' => 'gram',
                'stok' => 1500,
                'stok_minimum' => 800,
            ],
            [
                'nama' => 'Mentega',
                'satuan' => 'gram',
                'stok' => 2000,
                'stok_minimum' => 1000,
            ],
            [
                'nama' => 'Sedotan kertas',
                'satuan' => 'pcs',
                'stok' => 500,
                'stok_minimum' => 200,
            ],
            [
                'nama' => 'Cup plastik 16oz',
                'satuan' => 'pcs',
                'stok' => 350,
                'stok_minimum' => 150,
            ],
        ];

        foreach ($items as $item) {
            Bahan::updateOrCreate(
                ['nama' => $item['nama']],
                $item
            );
        }
    }
}
