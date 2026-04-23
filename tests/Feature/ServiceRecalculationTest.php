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
 * Verifies that Service::calculatePrice() correctly prices dynamic-slider
 * services using the ServiceConfig.slider_value column (Patch 3).
 */
class ServiceRecalculationTest extends TestCase
{
    use RefreshDatabase;

    private function buildSliderService(float $planBase = 5.0, float $sliderValue = 4.0): object
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'name' => 'Slider Product',
            'description' => 'Test',
        ]);

        $plan = Plan::factory()->create([
            'priceable_id' => $product->id,
            'priceable_type' => Product::class,
            'name' => 'Monthly',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
            'dynamic_slider_base_price' => $planBase,
        ]);

        Price::factory()->create([
            'plan_id' => $plan->id,
            'price' => 10.00,
            'setup_fee' => 0.00,
            'currency_code' => 'USD',
        ]);

        // 3 sliders, each with rate_per_unit=1.0
        $sliders = [];
        foreach (['Memory', 'CPU', 'Disk'] as $name) {
            $option = ConfigOption::create([
                'name' => $name,
                'env_variable' => strtoupper($name),
                'type' => 'dynamic_slider',
                'hidden' => false,
                'upgradable' => false,
                'metadata' => [
                    'resource_type' => strtolower($name),
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                    'default' => 2,
                    'unit' => 'units',
                    'display_unit' => 'units',
                    'display_divisor' => 1,
                    'pricing' => [
                        'model' => 'linear',
                        'base_price' => 0,
                        'rate_per_unit' => 1.0,
                    ],
                ],
            ]);

            ConfigOptionProduct::create([
                'product_id' => $product->id,
                'config_option_id' => $option->id,
            ]);

            $sliders[] = $option;
        }

        $service = Service::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'plan_id' => $plan->id,
            'status' => Service::STATUS_ACTIVE,
            'currency_code' => 'USD',
            'price' => 0, // will be recalculated
        ]);

        // Write ServiceConfig rows (as Cart.php would do after Patch 3)
        foreach ($sliders as $slider) {
            ServiceConfig::create([
                'configurable_id' => $service->id,
                'configurable_type' => Service::class,
                'config_option_id' => $slider->id,
                'config_value_id' => null,
                'slider_value' => $sliderValue,
            ]);
        }

        return (object) [
            'service' => $service,
            'plan' => $plan,
            'sliders' => $sliders,
            'user' => $user,
        ];
    }

    /**
     * Service with 3 sliders, each value=4, rate=1.0, plan_base=5, plan_price=10.
     * Expected: 10 + 5 + (3 * 4 * 1.0) = 27.00
     */
    public function test_calculate_price_includes_slider_deltas_and_base(): void
    {
        $fixture = $this->buildSliderService(planBase: 5.0, sliderValue: 4.0);

        $service = $fixture->service->fresh(['plan.prices', 'configs.configOption', 'properties']);

        $calculated = $service->calculatePrice();

        // plan=10, base=5, 3 sliders * 4 units * $1 = 12 => total = 27
        $this->assertEquals('27.00', $calculated);
    }

    /**
     * Service with no slider_value set should not add slider charges.
     */
    public function test_calculate_price_skips_slider_with_null_value(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['name' => 'P', 'description' => 'D']);
        $plan = Plan::factory()->create([
            'priceable_id' => $product->id,
            'priceable_type' => Product::class,
            'name' => 'M',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
            'dynamic_slider_base_price' => null,
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
                'pricing' => ['model' => 'linear', 'base_price' => 0, 'rate_per_unit' => 5.0],
                'display_divisor' => 1,
            ],
        ]);
        ConfigOptionProduct::create(['product_id' => $product->id, 'config_option_id' => $option->id]);

        $service = Service::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'plan_id' => $plan->id,
            'status' => Service::STATUS_ACTIVE,
            'currency_code' => 'USD',
            'price' => 0,
        ]);

        // ServiceConfig row with null slider_value (no value set)
        ServiceConfig::create([
            'configurable_id' => $service->id,
            'configurable_type' => Service::class,
            'config_option_id' => $option->id,
            'config_value_id' => null,
            'slider_value' => null,
        ]);

        $service = $service->fresh(['plan.prices', 'configs.configOption', 'properties']);
        $calculated = $service->calculatePrice();

        // Only plan price, no slider charge
        $this->assertEquals('10.00', $calculated);
    }
}
