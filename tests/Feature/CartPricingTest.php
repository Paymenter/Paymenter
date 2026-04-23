<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ConfigOption;
use App\Models\ConfigOptionProduct;
use App\Models\Plan;
use App\Models\Price;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifies that a 3-slider product charges the shared base price exactly once
 * (not once per slider) and that the cart total matches the expected amount.
 */
class CartPricingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build a product with 3 dynamic_slider config options, each with base_price=5.
     * The plan has dynamic_slider_base_price=5 (collapsed from per-slider).
     * Each slider has rate_per_unit=1.0 and display_divisor=1.
     */
    private function buildThreeSliderProduct(): object
    {
        $product = Product::factory()->create([
            'name' => '3-Slider Product',
            'description' => 'Test product with 3 dynamic sliders',
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

        $sliders = [];
        $sliderNames = ['Memory', 'CPU', 'Disk'];

        foreach ($sliderNames as $i => $name) {
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
                        'base_price' => 0, // collapsed to plan level
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

        return (object) [
            'product' => $product,
            'plan' => $plan,
            'sliders' => $sliders,
        ];
    }

    /**
     * Cart total for a 3-slider product should be:
     *   plan_price + plan->dynamicSliderBasePrice() + sum(slider_deltas)
     *   = 10 + 5 + (3 * 2 * 1.0) = 21
     * NOT 10 + (3 * (5 + 2)) = 31 (old broken behaviour).
     */
    public function test_cart_item_price_adds_base_once_for_three_sliders(): void
    {
        $fixture = $this->buildThreeSliderProduct();

        $cart = Cart::create([
            'currency_code' => 'USD',
        ]);

        // Each slider value = 2 units
        $configOptions = collect($fixture->sliders)->map(fn ($slider) => [
            'option_id' => $slider->id,
            'option_type' => 'dynamic_slider',
            'option_name' => $slider->name,
            'option_env_variable' => $slider->env_variable,
            'value' => 2,
        ])->values()->toArray();

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $fixture->product->id,
            'plan_id' => $fixture->plan->id,
            'config_options' => $configOptions,
            'checkout_config' => [],
            'quantity' => 1,
        ]);

        // Reload with relations
        $cartItem->load(['plan.prices', 'product.configOptions.children']);

        $price = $cartItem->price;

        // plan_price=10, base=5, 3 sliders * 2 units * $1/unit = 6 => total = 21
        $this->assertEquals(21.0, $price->price, 'Cart total should be 21 (base counted once)');
    }

    /**
     * Verify that dynamicSliderBasePrice() returns 0 when the column is null.
     */
    public function test_plan_dynamic_slider_base_price_defaults_to_zero(): void
    {
        $plan = Plan::factory()->create([
            'priceable_id' => Product::factory()->create()->id,
            'priceable_type' => Product::class,
            'name' => 'No-base Plan',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
            // dynamic_slider_base_price not set => null
        ]);

        $this->assertEquals(0.0, $plan->dynamicSliderBasePrice());
    }

    /**
     * Verify that dynamicSliderBasePrice() returns the configured value.
     */
    public function test_plan_dynamic_slider_base_price_returns_configured_value(): void
    {
        $plan = Plan::factory()->create([
            'priceable_id' => Product::factory()->create()->id,
            'priceable_type' => Product::class,
            'name' => 'Base Plan',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
            'dynamic_slider_base_price' => 7.50,
        ]);

        $this->assertEquals(7.50, $plan->dynamicSliderBasePrice());
    }
}
