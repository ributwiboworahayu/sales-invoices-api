<?php

namespace Database\Factories;

use App\Models\Uom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->word(),
            'name' => fake()->name(),
            'description' => fake()->text(100),
            'uom_id' => Uom::inRandomOrder()->first()->id,
            'price' => fake()->randomFloat(2, 1, 1000),
        ];
    }
}
