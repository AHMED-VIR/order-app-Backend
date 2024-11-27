<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>fake()->name(),
            'store_id'=>Store::inRandomOrder()->value('id'),
            'stock'=>fake()->numberBetween(20,300),
            'price'=>fake()->numberBetween(10,1000),
            'description'=>fake()->sentence()
        ];
    }
}
