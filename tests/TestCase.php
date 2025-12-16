<?php

namespace Tests;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected bool $seed = true;

    //
    protected function createProduct(array $attributes = [])
    {

        // Create product + plan + price
        $product = \App\Models\Product::factory()->create(array_merge([
            'name' => 'Test Product',
            'description' => 'This is a test product.',
        ], $attributes));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Test Product',
            'description' => 'This is a test product.',
        ]);

        $plan = \App\Models\Plan::factory()->create([
            'priceable_id' => $product->id,
            'priceable_type' => \App\Models\Product::class,
            'name' => 'Test Plan',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
        ]);

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'Test Plan',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
        ]);

        \App\Models\Price::factory()->create([
            'plan_id' => $plan->id,
            'price' => 10.00,
            'currency_code' => 'USD',
        ]);

        $this->assertDatabaseHas('prices', [
            'plan_id' => $plan->id,
            'price' => 10.00,
            'currency_code' => 'USD',
        ]);

        return (object) [
            'product' => $product,
            'plan' => $plan,
        ];
    }

    /**
     * Helper to create a user session and set it in the session
     */
    protected function loginUser(User $user, array $sessionData = []): array
    {
        $userSession = UserSession::create([
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 512),
            'last_activity' => now(),
            'expires_at' => null,
        ]);

        return array_merge([
            'user_session' => $userSession->ulid,
        ], $sessionData);
    }
}
