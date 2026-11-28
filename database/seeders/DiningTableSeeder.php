<?php

namespace Database\Seeders;

use App\Models\CafeTable;
use Illuminate\Database\Seeder;

class DiningTableSeeder extends Seeder
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
        ];

        foreach ($tables as $tbl) {
            CafeTable::updateOrCreate(
                ['table_number' => $tbl['table_number']],
                $tbl
            );
        }
    }
}
