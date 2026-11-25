<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DiningTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mejaList = ['Meja 01', 'Meja 02', 'Meja 03'];

        foreach ($mejaList as $meja) {
            DB::table('meja')->updateOrInsert(
                ['nomor_meja' => $meja],
                [
                    'token_qr' => Str::random(32),
                    'status_aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
