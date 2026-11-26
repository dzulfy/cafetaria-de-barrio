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
            CategorySeeder::class,
            MenuSeeder::class,
            DiningTableSeeder::class,
            BahanSeeder::class,
            DashboardSeeder::class,
            PengeluaranSeeder::class,
            CafeTableSeeder::class,
            OrderSeeder::class,
        ]);

        if (class_exists(WarkopDeBarrioSeeder::class)) {
            $this->call(WarkopDeBarrioSeeder::class);
        }
    }
}
