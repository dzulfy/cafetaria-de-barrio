<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => str()->slug($name),
            'description' => fake()->sentence(),
            'price' => fake()->randomElement([10000, 15000, 16000, 18000, 20000]),
            'category' => fake()->randomElement(['minuman', 'makanan']),
            'icon' => fake()->randomElement(['coffee', 'food']),
            'is_available' => true,
        ];
    }
}
