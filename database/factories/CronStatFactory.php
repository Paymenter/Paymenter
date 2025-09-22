<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CronStat>
 */
class CronStatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $keys = [
            'invoices_created',
            'orders_cancelled',
            'upgrade_invoices_updated',
            'services_suspended',
            'services_terminated',
            'tickets_closed',
            'email_logs_deleted',
        ];

        return [
            'key' => $this->faker->randomElement($keys),
            'value' => $this->faker->numberBetween(0, 100),
            'date' => $this->faker->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
        ];
    }
}
