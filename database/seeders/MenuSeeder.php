<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Kopi Susu Gula Aren',
                'slug' => 'kopi-susu-gula-aren',
                'description' => 'Espresso dengan susu segar dan gula aren murni khas nusantara.',
                'price' => 16000,
                'category' => 'minuman',
                'icon' => 'coffee',
                'is_available' => true,
            ],
            [
                'name' => 'Pisang Goreng',
                'slug' => 'pisang-goreng',
                'description' => 'Pisang kepok goreng renyah dengan rasa manis alami.',
                'price' => 10000,
                'category' => 'makanan',
                'icon' => 'food',
                'is_available' => true,
            ],
            [
                'name' => 'Caffe Latte',
                'slug' => 'caffe-latte',
                'description' => 'Kombinasi espresso yang lembut dengan steamed milk.',
                'price' => 18000,
                'category' => 'minuman',
                'icon' => 'coffee',
                'is_available' => true,
            ],
            [
                'name' => 'Croissant Butter',
                'slug' => 'croissant-butter',
                'description' => 'Pastry mentega renyah dan gurih khas kafe.',
                'price' => 15000,
                'category' => 'makanan',
                'icon' => 'food',
                'is_available' => true,
            ],
        ];

        foreach ($items as $item) {
            Menu::firstOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
