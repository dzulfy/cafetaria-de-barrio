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
        $categoriesData = [
            'Kopi' => [
                [
                    'name' => 'Kopi Susu Gula Aren',
                    'description' => 'Espresso, susu, gula aren',
                    'price' => 16000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Es Kopi Susu',
                    'description' => 'Kopi susu dingin klasik',
                    'price' => 15000,
                    'is_available' => true,
                ],
                [
                    'name' => 'V60 Manual Brew',
                    'description' => 'Single origin, seduh manual',
                    'price' => 18000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Americano',
                    'description' => 'Double shot espresso dengan air dingin segar',
                    'price' => 14000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Cappuccino',
                    'description' => 'Espresso dengan microfoam susu lembut',
                    'price' => 18000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Caramel Macchiato',
                    'description' => 'Espresso, sirup vanilla, susu creamy dan saus karamel',
                    'price' => 20000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Kopi Tubruk',
                    'description' => 'Kopi hitam tradisional seduh panas aroma kuat',
                    'price' => 10000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Kopi Tarik',
                    'description' => 'Kopi kental manis berbusa lembut khas melayu',
                    'price' => 13000,
                    'is_available' => true,
                ],
            ],
            'Non-Kopi' => [
                [
                    'name' => 'Matcha Latte',
                    'description' => 'Matcha premium Jepang dengan steamed milk gurih',
                    'price' => 18000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Es Teh Manis',
                    'description' => 'Teh dingin segar aroma melati',
                    'price' => 6000,
                    'is_available' => false, // Habis sesuai gambar mock-up!
                ],
                [
                    'name' => 'Red Velvet Latte',
                    'description' => 'Red velvet lembut dengan perpaduan susu segar',
                    'price' => 18000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Chocolate Signature',
                    'description' => 'Cokelat pekat lezat kaya rasa khas De Barrio',
                    'price' => 17000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Lemon Tea',
                    'description' => 'Teh hitam segar dipadukan sari lemon asli',
                    'price' => 12000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Earl Grey Milk Tea',
                    'description' => 'Teh bergamot aromatik dengan sentuhan susu manis',
                    'price' => 19000,
                    'is_available' => true,
                ],
            ],
            'Makanan Ringan' => [
                [
                    'name' => 'Pisang Goreng',
                    'description' => 'Renyah, porsi 4 potong dengan taburan gula aren',
                    'price' => 10000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Roti Bakar Coklat Keju',
                    'description' => 'Roti bakar empuk isi meses coklat & taburan keju melimpah',
                    'price' => 15000,
                    'is_available' => true,
                ],
                [
                    'name' => 'French Fries',
                    'description' => 'Kentang goreng renyah bumbu gurih disajikan dengan saus',
                    'price' => 14000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Cireng Krispi Sambal Rujak',
                    'description' => 'Cireng goreng renyah kenyal dengan cocolan sambal rujak',
                    'price' => 12000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Dimsum Ayam',
                    'description' => 'Dimsum daging ayam lembut isi 4 pcs dengan chili oil',
                    'price' => 16000,
                    'is_available' => true,
                ],
            ],
            'Makanan Berat' => [
                [
                    'name' => 'Nasi Goreng',
                    'description' => 'Nasi goreng kampung bumbu rempah plus telur dan kerupuk',
                    'price' => 18000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Mie Goreng Spesial',
                    'description' => 'Mie goreng gurih nikmat disajikan dengan telur mata sapi',
                    'price' => 16000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Rice Bowl Ayam Sambal Matah',
                    'description' => 'Nasi hangat dengan ayam krispi juicy dan sambal matah wangi',
                    'price' => 22000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Nasi Ayam Teriyaki',
                    'description' => 'Fillet ayam saus teriyaki manis gurih dan taburan wijen',
                    'price' => 22000,
                    'is_available' => true,
                ],
                [
                    'name' => 'Spaghetti Carbonara',
                    'description' => 'Pasta creamy ala Italia dengan smoked beef dan taburan keju',
                    'price' => 24000,
                    'is_available' => true,
                ],
            ],
        ];

        foreach ($categoriesData as $categoryName => $menus) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            foreach ($menus as $menu) {
                Menu::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $menu['name'],
                    ],
                    [
                        'description' => $menu['description'],
                        'price' => $menu['price'],
                        'is_available' => $menu['is_available'],
                    ]
                );
            }
        }
    }
}
