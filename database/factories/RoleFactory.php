<?php

namespace Database\Factories;

use App\Utils\Permissions;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $available = Permissions::$flags;
        $permissions = [];
        foreach ($available as $permission) {
            if(fake()->boolean()) $permissions[$permission];
        }
        $permission = Permissions::create($permissions);

        return [
            'name' => fake()->randomKey(['admin', 'user']),
            'permissions' => $permission,
        ];
    }
}
