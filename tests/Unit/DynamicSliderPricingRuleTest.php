<?php

namespace Tests\Unit;

use App\Rules\DynamicSliderPricingRule;
use PHPUnit\Framework\TestCase;

class DynamicSliderPricingRuleTest extends TestCase
{
    private function runRule(mixed $value): array
    {
        $errors = [];
        $rule = new DynamicSliderPricingRule();
        $rule->validate('metadata.pricing', $value, function (string $message) use (&$errors) {
            $errors[] = $message;
        });

        return $errors;
    }

    // --- Unknown / missing model ---

    public function test_unknown_model_fails(): void
    {
        $errors = $this->runRule(['model' => 'base_plus_addon', 'rate_per_unit' => 1.0]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('base_plus_addon', $errors[0]);
    }

    public function test_missing_model_fails(): void
    {
        $errors = $this->runRule(['rate_per_unit' => 1.0]);
        $this->assertNotEmpty($errors);
    }

    public function test_non_array_value_fails(): void
    {
        $errors = $this->runRule('not-an-array');
        $this->assertNotEmpty($errors);
    }

    // --- Linear model ---

    public function test_valid_linear_passes(): void
    {
        $errors = $this->runRule([
            'model'        => 'linear',
            'base_price'   => 5.0,
            'rate_per_unit' => 2.0,
        ]);
        $this->assertEmpty($errors);
    }

    public function test_linear_missing_rate_per_unit_fails(): void
    {
        $errors = $this->runRule([
            'model'      => 'linear',
            'base_price' => 5.0,
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('rate_per_unit', $errors[0]);
    }

    public function test_linear_negative_rate_fails(): void
    {
        $errors = $this->runRule([
            'model'        => 'linear',
            'rate_per_unit' => -1.0,
        ]);
        $this->assertNotEmpty($errors);
    }

    public function test_linear_negative_base_price_fails(): void
    {
        $errors = $this->runRule([
            'model'        => 'linear',
            'base_price'   => -5.0,
            'rate_per_unit' => 2.0,
        ]);
        $this->assertNotEmpty($errors);
    }

    // --- Tiered model ---

    public function test_valid_tiered_passes(): void
    {
        $errors = $this->runRule([
            'model' => 'tiered',
            'tiers' => [
                ['up_to' => 4,  'rate' => 3.0],
                ['up_to' => 16, 'rate' => 2.5],
                ['up_to' => null, 'rate' => 2.0],
            ],
        ]);
        $this->assertEmpty($errors);
    }

    public function test_tiered_out_of_order_up_to_fails(): void
    {
        $errors = $this->runRule([
            'model' => 'tiered',
            'tiers' => [
                ['up_to' => 16, 'rate' => 2.5],
                ['up_to' => 4,  'rate' => 3.0],
            ],
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('strictly greater', $errors[0]);
    }

    public function test_tiered_missing_tiers_fails(): void
    {
        $errors = $this->runRule(['model' => 'tiered']);
        $this->assertNotEmpty($errors);
    }

    public function test_tiered_empty_tiers_fails(): void
    {
        $errors = $this->runRule(['model' => 'tiered', 'tiers' => []]);
        $this->assertNotEmpty($errors);
    }

    public function test_tiered_missing_rate_fails(): void
    {
        $errors = $this->runRule([
            'model' => 'tiered',
            'tiers' => [
                ['up_to' => 4],
            ],
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('rate', $errors[0]);
    }

    public function test_tiered_negative_rate_fails(): void
    {
        $errors = $this->runRule([
            'model' => 'tiered',
            'tiers' => [
                ['up_to' => 4, 'rate' => -1.0],
            ],
        ]);
        $this->assertNotEmpty($errors);
    }

    // --- Base+Addon model ---

    public function test_valid_base_addon_passes(): void
    {
        $errors = $this->runRule([
            'model'          => 'base_addon',
            'included_units' => 4,
            'overage_rate'   => 2.5,
        ]);
        $this->assertEmpty($errors);
    }

    public function test_base_addon_missing_included_units_fails(): void
    {
        $errors = $this->runRule([
            'model'        => 'base_addon',
            'overage_rate' => 2.5,
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('included_units', $errors[0]);
    }

    public function test_base_addon_missing_overage_rate_fails(): void
    {
        $errors = $this->runRule([
            'model'          => 'base_addon',
            'included_units' => 4,
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('overage_rate', $errors[0]);
    }

    public function test_base_addon_negative_overage_rate_fails(): void
    {
        $errors = $this->runRule([
            'model'          => 'base_addon',
            'included_units' => 4,
            'overage_rate'   => -1.0,
        ]);
        $this->assertNotEmpty($errors);
    }

    // --- Numeric coercion guards (non-numeric values must be rejected) ---

    public function test_non_numeric_base_price_fails(): void
    {
        $errors = $this->runRule([
            'model'         => 'linear',
            'base_price'    => 'abc',
            'rate_per_unit' => 1.0,
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('numeric', $errors[0]);
    }

    public function test_non_numeric_rate_per_unit_fails(): void
    {
        $errors = $this->runRule([
            'model'         => 'linear',
            'rate_per_unit' => 'free',
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('numeric', $errors[0]);
    }

    public function test_non_numeric_tier_rate_fails(): void
    {
        $errors = $this->runRule([
            'model' => 'tiered',
            'tiers' => [
                ['up_to' => 4, 'rate' => 'free'],
            ],
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('numeric', $errors[0]);
    }

    public function test_non_numeric_tier_up_to_fails(): void
    {
        $errors = $this->runRule([
            'model' => 'tiered',
            'tiers' => [
                ['up_to' => 'unlimited', 'rate' => 1.0],
            ],
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('numeric', $errors[0]);
    }

    public function test_non_numeric_included_units_fails(): void
    {
        $errors = $this->runRule([
            'model'          => 'base_addon',
            'included_units' => 'four',
            'overage_rate'   => 1.0,
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('numeric', $errors[0]);
    }

    public function test_non_numeric_overage_rate_fails(): void
    {
        $errors = $this->runRule([
            'model'          => 'base_addon',
            'included_units' => 4,
            'overage_rate'   => 'free',
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('numeric', $errors[0]);
    }

    // --- Unlimited tier rules (only the last tier may omit up_to) ---

    public function test_tiered_unlimited_middle_tier_fails(): void
    {
        $errors = $this->runRule([
            'model' => 'tiered',
            'tiers' => [
                ['up_to' => 4,    'rate' => 3.0],
                ['up_to' => null, 'rate' => 2.5],   // unlimited mid-list — should fail
                ['up_to' => 16,   'rate' => 2.0],
            ],
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('last tier', $errors[0]);
    }

    public function test_tiered_unlimited_last_tier_passes(): void
    {
        $errors = $this->runRule([
            'model' => 'tiered',
            'tiers' => [
                ['up_to' => 4,    'rate' => 3.0],
                ['up_to' => 16,   'rate' => 2.5],
                ['up_to' => null, 'rate' => 2.0],
            ],
        ]);
        $this->assertEmpty($errors);
    }

    public function test_tiered_negative_up_to_fails(): void
    {
        $errors = $this->runRule([
            'model' => 'tiered',
            'tiers' => [
                ['up_to' => -5, 'rate' => 1.0],
                ['up_to' => 16, 'rate' => 2.0],
            ],
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('non-negative', $errors[0]);
    }

    public function test_tiered_empty_string_up_to_fails(): void
    {
        // Empty string is not the same as null/missing — it would coerce to 0 in
        // the runtime tier math. The validator must reject it explicitly.
        $errors = $this->runRule([
            'model' => 'tiered',
            'tiers' => [
                ['up_to' => '', 'rate' => 1.0],
                ['up_to' => 16, 'rate' => 2.0],
            ],
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('empty string', $errors[0]);
    }

    public function test_non_string_model_fails_gracefully(): void
    {
        // array_key_exists() TypeErrors when handed a non-scalar key (e.g. array).
        // The is_string() guard turns that crash into a clean validation error.
        $errors = $this->runRule([
            'model' => ['tiered'],
            'tiers' => [
                ['up_to' => 4, 'rate' => 1.0],
            ],
        ]);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Unknown', $errors[0]);
    }

}
