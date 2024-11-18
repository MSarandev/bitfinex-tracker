<?php

namespace Database\Factories;

use App\Models\PercentDelta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PercentDelta>
 */
class PercentDeltaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->numberBetween(1, 100000),
            'user_id' => fake()->numberBetween(1, 100000),
            'timeframe_flag' => 'H',
            'timeframe_value' => fake()->numberBetween(1, 23),
            'percent_change' => fake()->randomFloat(2, 1, 250),
            'active' => fake()->boolean(),
            'symbol' => fake()->randomElement(config('bitfinex.symbols')),
        ];
    }
}
