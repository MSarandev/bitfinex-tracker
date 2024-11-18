<?php

namespace Database\Factories;

use App\Models\PriceAction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PriceAction>
 */
class PriceActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->numberBetween(1, 100000),
            'user_id' => fake()->numberBetween(1, 100000),
            'trigger' => fake()->randomElement(['above', 'below']),
            'price' => fake()->randomFloat(2, 0, 1000),
            'active' => fake()->boolean(),
            'symbol' => fake()->randomElement(config('bitfinex.symbols')),
        ];
    }
}
