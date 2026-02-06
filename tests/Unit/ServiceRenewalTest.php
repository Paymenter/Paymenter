<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRenewalTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_duedate_is_set(): void
    {
        // Create a user
        $user = \App\Models\User::factory()->create();
        $product = $this->createProduct();

        // Create a subscription for the user
        $service = \App\Models\Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'pending',
            'currency_code' => 'USD',
            'price' => 10.00, // Set a price for the service
        ]);

        // Create an invoice for the service renewal
        $invoice = \App\Models\Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'currency_code' => 'USD',
        ]);
        $invoice->items()->create([
            'reference_id' => $service->id,
            'reference_type' => \App\Models\Service::class,
            'description' => 'Service Renewal',
            'quantity' => 1,
            'price' => 10.00,
        ]);

        // Process the paid invoice
        $invoice->transactions()->create([
            'amount' => 10.00,
        ]);

        $invoice->refresh();
        $service->refresh();

        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals('active', $service->status);
        $this->assertNotNull($service->expires_at);
    }

    public function test_service_duedate_is_extended_when_active(): void
    {
        // Create a user
        $user = \App\Models\User::factory()->create();
        $product = $this->createProduct();

        // Create a subscription for the user
        $service = \App\Models\Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'active',
            'expires_at' => now()->subDays(10),
            'currency_code' => 'USD',
            'price' => 10.00, // Set a price for the service
        ]);

        // Create an invoice for the service renewal
        $invoice = \App\Models\Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'currency_code' => 'USD',
        ]);

        $invoice->items()->create([
            'reference_id' => $service->id,
            'reference_type' => \App\Models\Service::class,
            'description' => 'Service Renewal',
            'quantity' => 1,
            'price' => 10.00,
        ]);

        // Process the paid invoice
        $invoice->transactions()->create([
            'amount' => 10.00,
        ]);

        $invoice->refresh();
        $service->refresh();

        $this->assertEquals('paid', $invoice->status);

        $this->assertEquals('active', $service->status);
        $this->assertNotNull($service->expires_at);
        $this->assertTrue($service->expires_at <= now()->addDays(21));
    }

    public function test_service_duedate_is_set_from_now_when_suspended(): void
    {
        // Create a user
        $user = \App\Models\User::factory()->create();
        $product = $this->createProduct();

        // Create a subscription for the user
        $service = \App\Models\Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'suspended',
            'expires_at' => now()->subDays(10),
            'currency_code' => 'USD',
            'price' => 10.00, // Set a price for the service
        ]);

        // Create an invoice for the service renewal
        $invoice = \App\Models\Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'currency_code' => 'USD',
        ]);

        $invoice->items()->create([
            'reference_id' => $service->id,
            'reference_type' => \App\Models\Service::class,
            'description' => 'Service Renewal',
            'quantity' => 1,
            'price' => 10.00,
        ]);

        // Process the paid invoice
        $invoice->transactions()->create([
            'amount' => 10.00,
        ]);

        $invoice->refresh();
        $service->refresh();

        $this->assertEquals('paid', $invoice->status);

        $this->assertEquals('active', $service->status);
        $this->assertNotNull($service->expires_at);

        $this->assertTrue($service->expires_at >= now()->addMonth()->addDay(-1)); // 30 days from now minus a few seconds for processing time
    }
}
