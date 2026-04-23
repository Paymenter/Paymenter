<?php

namespace Tests\Unit;

use App\Models\ConfigOption;
use PHPUnit\Framework\TestCase;

class ConfigOptionDynamicPricingTest extends TestCase
{
    private function createConfigOption(array $metadata): ConfigOption
    {
        $option = new ConfigOption();
        $option->type = 'dynamic_slider';
        $option->metadata = $metadata;

        return $option;
    }

    public function test_linear_pricing_calculates_correctly(): void
    {
        $option = $this->createConfigOption([
            'pricing' => [
                'model' => 'linear',
                'base_price' => 5.0,
                'rate_per_unit' => 2.0,
            ],
            'display_divisor' => 1,
        ]);

        $price = $option->calculateDynamicPrice(10, 1, 'month');
        $this->assertEquals(25.0, $price); // 5 + (10 * 2)
    }

    public function test_tiered_pricing_calculates_correctly(): void
    {
        $option = $this->createConfigOption([
            'pricing' => [
                'model' => 'tiered',
                'base_price' => 0,
                'tiers' => [
                    ['up_to' => 4, 'rate' => 3.0],
                    ['up_to' => 16, 'rate' => 2.5],
                    ['up_to' => null, 'rate' => 2.0],
                ],
            ],
            'display_divisor' => 1,
        ]);

        $price = $option->calculateDynamicPrice(10, 1, 'month');
        // (4 * 3) + (6 * 2.5) = 12 + 15 = 27
        $this->assertEquals(27.0, $price);
    }

    public function test_base_addon_pricing_calculates_correctly(): void
    {
        $option = $this->createConfigOption([
            'pricing' => [
                'model' => 'base_addon',
                'base_price' => 5.0,
                'included_units' => 4,
                'overage_rate' => 2.5,
            ],
            'display_divisor' => 1,
        ]);

        $price = $option->calculateDynamicPrice(10, 1, 'month');
        // 5 + ((10 - 4) * 2.5) = 5 + 15 = 20
        $this->assertEquals(20.0, $price);
    }

    public function test_unknown_model_throws_exception(): void
    {
        $option = $this->createConfigOption([
            'pricing' => [
                'model' => 'unknown_model',
                'rate_per_unit' => 1.0,
            ],
            'display_divisor' => 1,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('unknown_model');

        $option->calculateDynamicPrice(10, 1, 'month');
    }

    public function test_legacy_base_plus_addon_throws_exception(): void
    {
        $option = $this->createConfigOption([
            'pricing' => [
                'model' => 'base_plus_addon',
                'rate_per_unit' => 1.0,
            ],
            'display_divisor' => 1,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('base_plus_addon');

        $option->calculateDynamicPrice(10, 1, 'month');
    }

    public function test_non_dynamic_slider_returns_zero(): void
    {
        $option = new ConfigOption();
        $option->type = 'select';
        $option->metadata = [
            'pricing' => [
                'model' => 'linear',
                'rate_per_unit' => 2.0,
            ],
        ];

        $price = $option->calculateDynamicPrice(10, 1, 'month');
        $this->assertEquals(0.0, $price);
    }

    public function test_billing_period_multiplier_applies_correctly(): void
    {
        $option = $this->createConfigOption([
            'pricing' => [
                'model' => 'linear',
                'base_price' => 5.0,
                'rate_per_unit' => 2.0,
            ],
            'display_divisor' => 1,
        ]);

        // Monthly billing (period=1, unit=month) = multiplier 1
        $monthlyPrice = $option->calculateDynamicPrice(10, 1, 'month');
        $this->assertEquals(25.0, $monthlyPrice);

        // 3-month billing = multiplier 3
        $quarterlyPrice = $option->calculateDynamicPrice(10, 3, 'month');
        $this->assertEquals(75.0, $quarterlyPrice);

        // Yearly billing = multiplier 12
        $yearlyPrice = $option->calculateDynamicPrice(10, 1, 'year');
        $this->assertEquals(300.0, $yearlyPrice);
    }

    public function test_display_divisor_applies_correctly(): void
    {
        $option = $this->createConfigOption([
            'pricing' => [
                'model' => 'linear',
                'base_price' => 0,
                'rate_per_unit' => 2.0,
            ],
            'display_divisor' => 1024, // MB to GB conversion
        ]);

        // 2048 MB with divisor 1024 = 2 GB displayed
        $price = $option->calculateDynamicPrice(2048, 1, 'month');
        $this->assertEquals(4.0, $price); // 2 GB * $2/GB
    }
    // -----------------------------------------------------------------------
    // Patch 1: calculateDynamicPriceDelta() — marginal only, no base_price
    // -----------------------------------------------------------------------

    public function test_delta_excludes_base_price_for_linear(): void
    {
        $option = $this->createConfigOption([
            'pricing' => [
                'model' => 'linear',
                'base_price' => 5.0,
                'rate_per_unit' => 2.0,
            ],
            'display_divisor' => 1,
        ]);

        $delta = $option->calculateDynamicPriceDelta(10, 1, 'month');
        // Marginal only: 10 * 2 = 20 (no base_price)
        $this->assertEquals(20.0, $delta);
    }

    public function test_delta_excludes_base_price_for_tiered(): void
    {
        $option = $this->createConfigOption([
            'pricing' => [
                'model' => 'tiered',
                'base_price' => 10.0,
                'tiers' => [
                    ['up_to' => 4, 'rate' => 3.0],
                    ['up_to' => null, 'rate' => 2.0],
                ],
            ],
            'display_divisor' => 1,
        ]);

        $delta = $option->calculateDynamicPriceDelta(6, 1, 'month');
        // (4 * 3) + (2 * 2) = 12 + 4 = 16 (no base_price)
        $this->assertEquals(16.0, $delta);
    }

    public function test_delta_excludes_base_price_for_base_addon(): void
    {
        $option = $this->createConfigOption([
            'pricing' => [
                'model' => 'base_addon',
                'base_price' => 5.0,
                'included_units' => 4,
                'overage_rate' => 2.5,
            ],
            'display_divisor' => 1,
        ]);

        $delta = $option->calculateDynamicPriceDelta(10, 1, 'month');
        // (10 - 4) * 2.5 = 15 (no base_price)
        $this->assertEquals(15.0, $delta);
    }

    /**
     * 3-slider product with base_price=5 on each slider.
     * When plan-level base is set, total should add exactly 5 (not 15).
     * Simulates: plan_price + plan->dynamicSliderBasePrice() + sum(deltas).
     */
    public function test_three_sliders_with_shared_base_adds_base_once(): void
    {
        $sliders = [];
        for ($i = 0; $i < 3; $i++) {
            $sliders[] = $this->createConfigOption([
                'pricing' => [
                    'model' => 'linear',
                    'base_price' => 5.0,
                    'rate_per_unit' => 1.0,
                ],
                'display_divisor' => 1,
            ]);
        }

        // Simulate plan-level base price = 5 (collapsed from per-slider)
        $planBase = 5.0;
        $planPrice = 10.0;

        // Each slider value = 2 units => delta = 2 * 1 = 2 per slider
        $totalDelta = array_sum(array_map(fn ($s) => $s->calculateDynamicPriceDelta(2, 1, 'month'), $sliders));

        $total = $planPrice + $planBase + $totalDelta;

        // Expected: 10 + 5 + (3 * 2) = 21 (base counted once, not 3x)
        $this->assertEquals(21.0, $total);

        // Verify the old (broken) behaviour would have been 10 + 15 + 6 = 31
        $oldTotal = $planPrice + array_sum(array_map(fn ($s) => $s->calculateDynamicPrice(2, 1, 'month'), $sliders));
        $this->assertEquals(31.0, $oldTotal, 'Sanity: deprecated alias still returns delta+base per slider');
    }

    public function test_deprecated_alias_returns_delta_plus_base(): void
    {
        $option = $this->createConfigOption([
            'pricing' => [
                'model' => 'linear',
                'base_price' => 5.0,
                'rate_per_unit' => 2.0,
            ],
            'display_divisor' => 1,
        ]);

        $delta = $option->calculateDynamicPriceDelta(10, 1, 'month');
        $full = $option->calculateDynamicPrice(10, 1, 'month');

        // Full = delta + base = 20 + 5 = 25
        $this->assertEquals(25.0, $full);
        $this->assertEquals(20.0, $delta);
        $this->assertEquals($full, $delta + 5.0);
    }
}
