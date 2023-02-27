<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code' => fake()->name(),
            'type' => fake()->name(),
            'value' => fake()->randomFloat(2, 0, 100),
            'max_uses' => fake()->numberBetween(1, 10),
            'uses' => fake()->numberBetween(1, 10),
            'start_date' => fake()->dateTime(),
            'end_date' => fake()->dateTime(),
            'status' => fake()->boolean(),
            'time' => fake()->numberBetween(1, 10),
        ];
    }
}