<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CafeTable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CafeTable>
 */
class CafeTableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $num = (string) fake()->unique()->numberBetween(1, 99);

        return [
            'table_number' => str_pad($num, 2, '0', STR_PAD_LEFT),
            'name' => 'Meja '.str_pad($num, 2, '0', STR_PAD_LEFT),
            'token' => Str::random(10),
            'status' => 'aktif',
        ];
    }
}
