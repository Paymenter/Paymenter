<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'type' => $this->faker->randomElement(['free', 'recurring', 'one-time']),
            'billing_period' => $this->faker->randomDigitNotNull(), // Billing period in months
            'billing_unit' => $this->faker->randomElement(['month', 'year']),
        ];
    }
}
