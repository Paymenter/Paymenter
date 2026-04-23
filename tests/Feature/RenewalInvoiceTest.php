<?php

namespace Tests\Feature;

use App\Models\ConfigOption;
use App\Models\ConfigOptionProduct;
use App\Models\Plan;
use App\Models\Price;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifies that the renewal cron generates an invoice with the correct price
 * for a dynamic-slider service (Patch 3).
 */
class RenewalInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_renewal_invoice_matches_slider_price(): void
    {
        config(['settings.cronjob_invoice' => 7]);

        $user = User::factory()->create();

        $product = Product::factory()->create([
            'name' => 'Slider Renewal Product',
            'description' => 'Test',
        ]);

        $plan = Plan::factory()->create([
            'priceable_id' => $product->id,
            'priceable_type' => Product::class,
            'name' => 'Monthly',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
            'dynamic_slider_base_price' => 5.00,
        ]);

        Price::factory()->create([
            'plan_id' => $plan->id,
            'price' => 10.00,
            'setup_fee' => 0.00,
            'currency_code' => 'USD',
        ]);

        $option = ConfigOption::create([
            'name' => 'Memory',
            'env_variable' => 'MEMORY',
            'type' => 'dynamic_slider',
            'hidden' => false,
            'upgradable' => false,
            'metadata' => [
                'resource_type' => 'memory',
                'min' => 1,
                'max' => 100,
                'step' => 1,
                'default' => 4,
                'unit' => 'units',
                'display_unit' => 'units',
                'display_divisor' => 1,
                'pricing' => [
                    'model' => 'linear',
                    'base_price' => 0,
                    'rate_per_unit' => 2.0,
                ],
            ],
        ]);

        ConfigOptionProduct::create([
            'product_id' => $product->id,
            'config_option_id' => $option->id,
        ]);

        // Expected price: plan=10, base=5, slider(4 units * $2) = 8 => total = 23
        $expectedPrice = '23.00';

        $service = Service::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'plan_id' => $plan->id,
            'status' => Service::STATUS_ACTIVE,
            'currency_code' => 'USD',
            'price' => $expectedPrice,
            'expires_at' => now()->addDays(2)->subHour(), // due within cronjob window
        ]);

        // Write the ServiceConfig row (as Patch 3 Cart.php would)
        ServiceConfig::create([
            'configurable_id' => $service->id,
            'configurable_type' => Service::class,
            'config_option_id' => $option->id,
            'config_value_id' => null,
            'slider_value' => 4.0,
        ]);

        // Run the cron job
        $this->artisan('app:cron-job')->assertExitCode(0);

        // Invoice should be created with the correct price
        $this->assertDatabaseHas('invoices', [
            'user_id' => $user->id,
            'status' => 'pending',
            'currency_code' => 'USD',
        ]);

        // The invoice item should reference the service with the correct price
        $invoice = $service->invoices()->latest()->first();
        $this->assertNotNull($invoice, 'Invoice should have been created');

        $item = $invoice->items()->first();
        $this->assertNotNull($item, 'Invoice item should exist');
        $this->assertEquals($expectedPrice, number_format((float) $item->price, 2, '.', ''));
    }
}
