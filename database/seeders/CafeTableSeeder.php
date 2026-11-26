<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CafeTable;
use Illuminate\Database\Seeder;

class CafeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            ['table_number' => '01', 'name' => 'Meja 01', 'token' => 'x7Kp9aLm2Q', 'status' => 'aktif'],
            ['table_number' => '02', 'name' => 'Meja 02', 'token' => 'b3Td8vNq5R', 'status' => 'aktif'],
            ['table_number' => '03', 'name' => 'Meja 03', 'token' => 'm9Ws1cHy7E', 'status' => 'aktif'],
            ['table_number' => '04', 'name' => 'Meja 04', 'token' => 'k2Lf6zXp4A', 'status' => 'aktif'],
            ['table_number' => '05', 'name' => 'Meja 05', 'token' => 'p8Gh3uJr1N', 'status' => 'aktif'],
            ['table_number' => '06', 'name' => 'Meja 06', 'token' => 't5Yv7eBd9C', 'status' => 'aktif'],
            ['table_number' => '07', 'name' => 'Meja 07', 'token' => 'w1Qn4sKm6Z', 'status' => 'aktif'],
            ['table_number' => '08', 'name' => 'Meja 08', 'token' => 'h6Rj2xFa8P', 'status' => 'nonaktif'],
        ];

        foreach ($tables as $table) {
            CafeTable::updateOrCreate(
                ['table_number' => $table['table_number']],
                $table
            );
        }
    }
}
