<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'nama' => 'Muhammad Fikri Firdaus',
                'email' => 'admin@debarrio.id',
                'peran' => 'admin',
            ],
            [
                'nama' => 'Ayu Lestari',
                'email' => 'kasir@debarrio.id',
                'peran' => 'kasir',
            ],
            [
                'nama' => 'Rina Anggraini',
                'email' => 'pelanggan@debarrio.id',
                'peran' => 'pelanggan',
            ],
        ];

        foreach ($users as $u) {
            DB::table('pengguna')->updateOrInsert(
                ['email' => $u['email']],
                [
                    'nama' => $u['nama'],
                    'password' => Hash::make('password'),
                    'peran' => $u['peran'],
                    'status_aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
