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
        DB::table('kategori')->delete();

        $kategoriList = [
            ['nama' => 'Minuman', 'urutan' => 1],
            ['nama' => 'Snack', 'urutan' => 2],
            ['nama' => 'Mie', 'urutan' => 3],
            ['nama' => 'Add On', 'urutan' => 4],
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
