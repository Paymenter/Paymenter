<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => $this->faker->randomFloat(2, 1),
            'currency_code' => 'USD',
            'status' => $this->faker->randomElement([
                \App\Models\Service::STATUS_PENDING,
                \App\Models\Service::STATUS_ACTIVE,
                \App\Models\Service::STATUS_CANCELLED,
                \App\Models\Service::STATUS_SUSPENDED,
            ]),
        ];
    }

}
