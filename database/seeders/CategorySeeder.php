<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Minuman', 'slug' => 'minuman', 'icon' => 'coffee'],
            ['name' => 'Snack', 'slug' => 'snack', 'icon' => 'food'],
            ['name' => 'Mie', 'slug' => 'mie', 'icon' => 'food'],
            ['name' => 'Add On', 'slug' => 'add-on', 'icon' => 'food'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                ]
            );
        }
    }
}
