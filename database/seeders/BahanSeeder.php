<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Bahan;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class BahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // 3 bahan dengan status Menipis (stok < stok_minimum) sebagai contoh peringatan
            [
                'nama' => 'Nescafe Sachet',
                'satuan' => 'sachet',
                'stok' => 18,
                'stok_minimum' => 25,
            ],
            [
                'nama' => 'Susu Kental Manis',
                'satuan' => 'ml',
                'stok' => 1800,
                'stok_minimum' => 2500,
            ],
            [
                'nama' => 'Kentang Beku (French Fries)',
                'satuan' => 'gram',
                'stok' => 1200,
                'stok_minimum' => 2000,
            ],

            // Bahan Minuman (sesuai daftar menu Minuman)
            [
                'nama' => 'Extra Joss Sachet',
                'satuan' => 'sachet',
                'stok' => 40,
                'stok_minimum' => 20,
            ],
            [
                'nama' => 'Kukubima Sachet',
                'satuan' => 'sachet',
                'stok' => 35,
                'stok_minimum' => 20,
            ],
            [
                'nama' => 'Nutrisari Sachet',
                'satuan' => 'sachet',
                'stok' => 45,
                'stok_minimum' => 20,
            ],
            [
                'nama' => 'Lemon Tea Bubuk',
                'satuan' => 'sachet',
                'stok' => 30,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Teh Tarik Sachet',
                'satuan' => 'sachet',
                'stok' => 36,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Bubuk Matcha',
                'satuan' => 'gram',
                'stok' => 600,
                'stok_minimum' => 300,
            ],
            [
                'nama' => 'Drink Beng Beng Sachet',
                'satuan' => 'sachet',
                'stok' => 32,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Good Day Freeze Sachet',
                'satuan' => 'sachet',
                'stok' => 38,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Good Day Cappuccino Sachet',
                'satuan' => 'sachet',
                'stok' => 34,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Kopi ABC Klepon Sachet',
                'satuan' => 'sachet',
                'stok' => 28,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Teajus Sachet',
                'satuan' => 'sachet',
                'stok' => 60,
                'stok_minimum' => 25,
            ],
            [
                'nama' => 'Dancow Putih Sachet',
                'satuan' => 'sachet',
                'stok' => 30,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Dancow Coklat Sachet',
                'satuan' => 'sachet',
                'stok' => 30,
                'stok_minimum' => 15,
            ],

            // Bahan Snack & Add On
            [
                'nama' => 'Nugget Ayam',
                'satuan' => 'pcs',
                'stok' => 90,
                'stok_minimum' => 40,
            ],
            [
                'nama' => 'Sosis Ayam/Sapi',
                'satuan' => 'pcs',
                'stok' => 75,
                'stok_minimum' => 30,
            ],
            [
                'nama' => 'Telur Ayam',
                'satuan' => 'butir',
                'stok' => 48,
                'stok_minimum' => 24,
            ],
            [
                'nama' => 'Minyak Goreng',
                'satuan' => 'ml',
                'stok' => 4000,
                'stok_minimum' => 2000,
            ],
            [
                'nama' => 'Saus Sambal & Tomat',
                'satuan' => 'ml',
                'stok' => 1500,
                'stok_minimum' => 500,
            ],

            // Bahan Mie (Indomie & Mie Sedap)
            [
                'nama' => 'Indomie Goreng',
                'satuan' => 'bungkus',
                'stok' => 40,
                'stok_minimum' => 20,
            ],
            [
                'nama' => 'Indomie Ayam Bawang',
                'satuan' => 'bungkus',
                'stok' => 32,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Indomie Aceh',
                'satuan' => 'bungkus',
                'stok' => 26,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Indomie Rendang',
                'satuan' => 'bungkus',
                'stok' => 28,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Mie Sedap Goreng',
                'satuan' => 'bungkus',
                'stok' => 35,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Mie Sedap Ayam Bawang',
                'satuan' => 'bungkus',
                'stok' => 30,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Mie Sedap Kari Ayam',
                'satuan' => 'bungkus',
                'stok' => 24,
                'stok_minimum' => 15,
            ],
            [
                'nama' => 'Mie Sedap Soto',
                'satuan' => 'bungkus',
                'stok' => 25,
                'stok_minimum' => 15,
            ],

            // Bahan Penunjang Minuman & Kemasan
            [
                'nama' => 'Gula Pasir',
                'satuan' => 'gram',
                'stok' => 3000,
                'stok_minimum' => 1000,
            ],
            [
                'nama' => 'Es Batu Kristal',
                'satuan' => 'gram',
                'stok' => 8000,
                'stok_minimum' => 3000,
            ],
            [
                'nama' => 'Cup & Sedotan 16oz',
                'satuan' => 'pcs',
                'stok' => 250,
                'stok_minimum' => 100,
            ],
        ];

        $bahanByName = [];
        foreach ($items as $item) {
            $existing = Bahan::query()
                ->whereRaw('LOWER(nama) = ?', [mb_strtolower($item['nama'])])
                ->first();

            if ($existing) {
                $existing->update($item);
                $bahanByName[$item['nama']] = $existing;
            } else {
                $bahanByName[$item['nama']] = Bahan::create($item);
            }
        }

        $minumanSlugs = [
            'nescafe',
            'josu',
            'nutrisari',
            'lemon-tea',
            'teh-tarik',
            'matcha',
            'drink-beng-beng',
            'kukubima-susu',
            'goodday-freeze',
            'goodday-cappucino',
            'abc-klepon',
            'teajus',
            'dancow-putih',
            'dancow-coklat',
        ];

        $recipeMap = [
            'Nescafe Sachet' => ['nescafe' => 1],
            'Susu Kental Manis' => ['josu' => 40, 'kukubima-susu' => 40, 'teh-tarik' => 35],
            'Kentang Beku (French Fries)' => ['french-fries' => 150, 'mix-platter' => 100],
            'Extra Joss Sachet' => ['josu' => 1],
            'Kukubima Sachet' => ['kukubima-susu' => 1],
            'Nutrisari Sachet' => ['nutrisari' => 1],
            'Lemon Tea Bubuk' => ['lemon-tea' => 1],
            'Teh Tarik Sachet' => ['teh-tarik' => 1],
            'Bubuk Matcha' => ['matcha' => 20],
            'Drink Beng Beng Sachet' => ['drink-beng-beng' => 1],
            'Good Day Freeze Sachet' => ['goodday-freeze' => 1],
            'Good Day Cappuccino Sachet' => ['goodday-cappucino' => 1],
            'Kopi ABC Klepon Sachet' => ['abc-klepon' => 1],
            'Teajus Sachet' => ['teajus' => 1],
            'Dancow Putih Sachet' => ['dancow-putih' => 1],
            'Dancow Coklat Sachet' => ['dancow-coklat' => 1],
            'Nugget Ayam' => ['nugget' => 6, 'mix-platter' => 3, 'add-on-nugget' => 3],
            'Sosis Ayam/Sapi' => ['sosis' => 4, 'mix-platter' => 2, 'add-on-sosis' => 2],
            'Telur Ayam' => ['add-on-telor' => 1],
            'Minyak Goreng' => [
                'french-fries' => 30,
                'nugget' => 30,
                'sosis' => 25,
                'mix-platter' => 40,
                'add-on-telor' => 15,
                'add-on-sosis' => 15,
                'add-on-nugget' => 15,
            ],
            'Saus Sambal & Tomat' => ['french-fries' => 20, 'nugget' => 20, 'sosis' => 20, 'mix-platter' => 30],
            'Indomie Goreng' => ['indomie-goreng' => 1],
            'Indomie Ayam Bawang' => ['indomie-ayam-bawang' => 1],
            'Indomie Aceh' => ['indomie-aceh' => 1],
            'Indomie Rendang' => ['indomie-rendang' => 1],
            'Mie Sedap Goreng' => ['mie-sedap-goreng' => 1],
            'Mie Sedap Ayam Bawang' => ['mie-sedap-ayam-bawang' => 1],
            'Mie Sedap Kari Ayam' => ['mie-sedap-kari-ayam' => 1],
            'Mie Sedap Soto' => ['mie-sedap-soto' => 1],
            'Gula Pasir' => ['nescafe' => 15, 'matcha' => 15, 'teajus' => 15, 'lemon-tea' => 10],
            'Es Batu Kristal' => array_fill_keys($minumanSlugs, 100),
            'Cup & Sedotan 16oz' => array_fill_keys($minumanSlugs, 1),
        ];

        $menusBySlug = Menu::all()->keyBy('slug');
        if ($menusBySlug->isEmpty()) {
            return;
        }

        foreach ($recipeMap as $bahanName => $menuQuantities) {
            $bahan = $bahanByName[$bahanName] ?? null;
            if (! $bahan) {
                continue;
            }

            $pivotData = [];
            foreach ($menuQuantities as $slug => $jumlahPakai) {
                if (isset($menusBySlug[$slug])) {
                    $pivotData[$menusBySlug[$slug]->id] = ['jumlah_pakai' => $jumlahPakai];
                }
            }

            if ($pivotData !== []) {
                $bahan->menu()->sync($pivotData);
            }
        }
    }
}
