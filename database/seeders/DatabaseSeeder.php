<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CafeTableSeeder::class,
            CategorySeeder::class,
            MenuSeeder::class,
            BahanSeeder::class,
            PengeluaranSeeder::class,
            DashboardSeeder::class,
        ]);

        if (class_exists(WarkopDeBarrioSeeder::class)) {
            $this->call(WarkopDeBarrioSeeder::class);
        }
    }
}
