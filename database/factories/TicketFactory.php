<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => $this->faker->sentence(1),
            'status' => $this->faker->randomElement(['active', 'replied', 'closed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'department' => $this->faker->word(),
        ];
    }
}
