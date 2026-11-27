<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::query()->delete();
        Category::query()->delete();

        $categories = [
            'minuman' => Category::create([
                'name' => 'Minuman',
                'slug' => 'minuman',
                'icon' => 'coffee',
            ]),
            'snack' => Category::create([
                'name' => 'Snack',
                'slug' => 'snack',
                'icon' => 'food',
            ]),
            'mie' => Category::create([
                'name' => 'Mie',
                'slug' => 'mie',
                'icon' => 'food',
            ]),
            'add-on' => Category::create([
                'name' => 'Add On',
                'slug' => 'add-on',
                'icon' => 'food',
            ]),
        ];

        $menus = [
            // Minuman (14 item)
            [
                'category_key' => 'minuman',
                'name' => 'Nescafe',
                'description' => 'Kopi instan Nescafe klasik dengan aroma mantap, disajikan hangat atau dingin.',
                'price' => 7000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Josu',
                'description' => 'Perpaduan segar Extra Joss dengan susu kental manis yang creamy.',
                'price' => 10000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Nutrisari',
                'description' => 'Minuman buah segar rasa jeruk pilihan pelepas dahaga.',
                'price' => 7000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Lemon Tea',
                'description' => 'Teh pilihan berpadu dengan kesegaran rasa lemon yang menyegarkan.',
                'price' => 8000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Teh Tarik',
                'description' => 'Teh susu khas ala warkop dengan tekstur lembut dan rasa manis pas.',
                'price' => 8000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Matcha',
                'description' => 'Minuman green tea matcha creamy yang lembut dan menenangkan.',
                'price' => 8000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Drink Beng Beng',
                'description' => 'Minuman cokelat karamel malt nikmat ala Beng-Beng favorit anak kos.',
                'price' => 9000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Kukubima Susu',
                'description' => 'Minuman energi Kukubima berpadu dengan susu kental manis penyegar stamina.',
                'price' => 10000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Goodday Freeze',
                'description' => 'Kopi dingin Good Day Freeze yang menyegarkan untuk teman nongkrong.',
                'price' => 9000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Goodday Cappucino',
                'description' => 'Kopi cappuccino nikmat dengan taburan choco granule di atasnya.',
                'price' => 9000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'ABC Klepon',
                'description' => 'Minuman kopi susu unik dengan cita rasa pandan gula aren khas klepon.',
                'price' => 9000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Teajus',
                'description' => 'Es teh manis hemat dan menyegarkan untuk menemani makanan.',
                'price' => 6000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Dancow Putih',
                'description' => 'Susu Dancow putih gurih dan bergizi, nikmat diminum hangat atau es.',
                'price' => 10000,
                'icon' => 'coffee',
            ],
            [
                'category_key' => 'minuman',
                'name' => 'Dancow Coklat',
                'description' => 'Susu cokelat Dancow manis creamy favorit semua kalangan.',
                'price' => 10000,
                'icon' => 'coffee',
            ],

            // Snack (4 item)
            [
                'category_key' => 'snack',
                'name' => 'French Fries',
                'description' => 'Kentang goreng renyah di luar, lembut di dalam, lengkap dengan saus sambal.',
                'price' => 12000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'snack',
                'name' => 'Nugget',
                'description' => 'Nugget ayam goreng krispi gurih untuk teman ngobrol.',
                'price' => 12000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'snack',
                'name' => 'Sosis',
                'description' => 'Sosis goreng gurih siap santap lengkap dengan cocolan saus.',
                'price' => 12000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'snack',
                'name' => 'Mix Platter',
                'description' => 'Paket lengkap berisi kentang goreng, sosis, dan nugget dalam satu porsi.',
                'price' => 15000,
                'icon' => 'food',
            ],

            // Mie (8 item)
            [
                'category_key' => 'mie',
                'name' => 'Indomie Goreng',
                'description' => 'Indomie goreng legendaris dengan bawang goreng renyah dan bumbu autentik.',
                'price' => 10000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'mie',
                'name' => 'Indomie Ayam Bawang',
                'description' => 'Indomie kuah hangat rasa ayam bawang yang gurih menenangkan.',
                'price' => 10000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'mie',
                'name' => 'Indomie Aceh',
                'description' => 'Indomie goreng mie tebal berbumbu rempah khas Aceh yang kaya rasa.',
                'price' => 10000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'mie',
                'name' => 'Indomie Rendang',
                'description' => 'Indomie goreng dengan bumbu rendang padang yang gurih dan sedap.',
                'price' => 10000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'mie',
                'name' => 'Mie Sedap Goreng',
                'description' => 'Mie Sedaap goreng klasik dengan taburan kriuk bawang goreng spesial.',
                'price' => 10000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'mie',
                'name' => 'Mie Sedap Ayam Bawang',
                'description' => 'Mie Sedaap kuah kaldu ayam bawang hangat yang nikmat disantap kapan saja.',
                'price' => 10000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'mie',
                'name' => 'Mie Sedap Kari Ayam',
                'description' => 'Mie Sedaap kuah kental rasa kari ayam berempah yang menggugah selera.',
                'price' => 10000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'mie',
                'name' => 'Mie Sedap Soto',
                'description' => 'Mie Sedaap kuah soto segar lengkap dengan serbuk koya gurih.',
                'price' => 10000,
                'icon' => 'food',
            ],

            // Add On (3 item)
            [
                'category_key' => 'add-on',
                'slug' => 'add-on-telor',
                'name' => 'Telor',
                'description' => 'Tambahan telur ceplok atau rebus untuk melengkapi hidangan mie.',
                'price' => 5000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'add-on',
                'slug' => 'add-on-sosis',
                'name' => 'Sosis',
                'description' => 'Tambahan topping sosis goreng gurih.',
                'price' => 5000,
                'icon' => 'food',
            ],
            [
                'category_key' => 'add-on',
                'slug' => 'add-on-nugget',
                'name' => 'Nugget',
                'description' => 'Tambahan topping nugget ayam krispi.',
                'price' => 5000,
                'icon' => 'food',
            ],
        ];

        foreach ($menus as $item) {
            $category = $categories[$item['category_key']];
            $slug = $item['slug'] ?? Str::slug($item['name']);

            Menu::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'category' => $category->slug,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'icon' => $item['icon'],
                    'is_available' => true,
                ]
            );
        }
    }
}
