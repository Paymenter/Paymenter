<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected bool $seed = true;

    //
    protected function createProduct()
    {
        // Create product + plan + price
        $product = \App\Models\Product::factory()->create([
            'name' => 'Test Product',
            'description' => 'This is a test product.',
        ]);

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
}
