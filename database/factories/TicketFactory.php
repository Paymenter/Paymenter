<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->name(),
            'status' => fake()->randomKey(['open', 'closed', 'pending']),
            'user_id' => fake()->numberBetween(1, 10),
            'order_id' => fake()->numberBetween(1, 10),
            'priority' => fake()->randomKey(['low', 'medium', 'high']),
        ];
    }
}