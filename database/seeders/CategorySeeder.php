<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriList = [
            ['nama' => 'Kopi', 'urutan' => 1],
            ['nama' => 'Non-Kopi', 'urutan' => 2],
            ['nama' => 'Makanan Ringan', 'urutan' => 3],
            ['nama' => 'Makanan Berat', 'urutan' => 4],
        ];

        foreach ($kategoriList as $kat) {
            DB::table('kategori')->updateOrInsert(
                ['nama' => $kat['nama']],
                [
                    'urutan' => $kat['urutan'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
