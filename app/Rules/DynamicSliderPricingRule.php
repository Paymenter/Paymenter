<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DynamicSliderPricingRule implements ValidationRule
{
    /**
     * Recognized pricing models and their required keys.
     */
    private const REQUIRED_KEYS = [
        'linear'     => ['rate_per_unit'],
        'tiered'     => ['tiers'],
        'base_addon' => ['included_units', 'overage_rate'],
    ];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail('The pricing configuration must be an array.');

            return;
        }

        $model = $value['model'] ?? null;

        // Reject unknown / missing / non-string model names. is_string() guards against array_key_exists()
        // throwing TypeError when $model is e.g. an array (it accepts null/string/int only).
        if (! is_string($model) || ! array_key_exists($model, self::REQUIRED_KEYS)) {
            $fail(
                'Unknown dynamic_slider pricing model "' . var_export($model, true) . '". '
                . 'Allowed values: ' . implode(', ', array_keys(self::REQUIRED_KEYS)) . '.'
            );

            return;
        }

        // Validate base_price is non-negative when present
        if (array_key_exists('base_price', $value) && $value['base_price'] !== null && $value['base_price'] !== '') {
            if (! is_numeric($value['base_price'])) {
                $fail('The base price must be numeric.');

                return;
            }
            if ((float) $value['base_price'] < 0) {
                $fail('The base price must be 0 or greater.');

                return;
            }
        }

        // Check required keys per model
        foreach (self::REQUIRED_KEYS[$model] as $key) {
            if (! array_key_exists($key, $value)) {
                $fail("The pricing configuration is missing required key \"{$key}\" for model \"{$model}\".");

                return;
            }
        }

        // Model-specific validation
        match ($model) {
            'linear'     => $this->validateLinear($value, $fail),
            'tiered'     => $this->validateTiered($value, $fail),
            'base_addon' => $this->validateBaseAddon($value, $fail),
        };
    }

    private function validateLinear(array $pricing, Closure $fail): void
    {
        if (! is_numeric($pricing['rate_per_unit'])) {
            $fail('The rate per unit must be numeric.');

            return;
        }

        if ((float) $pricing['rate_per_unit'] < 0) {
            $fail('The rate per unit must be 0 or greater.');
        }
    }

    private function validateTiered(array $pricing, Closure $fail): void
    {
        $tiers = $pricing['tiers'] ?? [];

        if (! is_array($tiers) || count($tiers) === 0) {
            $fail('Tiered pricing must have at least one tier.');

            return;
        }

        $previousUpTo = -1;
        $lastIndex    = array_key_last($tiers);

        foreach ($tiers as $index => $tier) {
            if (! is_array($tier)) {
                $fail('Each tier must be an array with "up_to" and "rate" keys.');

                return;
            }
            $tierNum = (int) $index + 1;

            if (! array_key_exists('rate', $tier)) {
                $fail("Tier {$tierNum} is missing a required \"rate\" value.");

                return;
            }

            if (! is_numeric($tier['rate'])) {
                $fail("Tier {$tierNum} rate must be numeric.");

                return;
            }

            if ((float) $tier['rate'] < 0) {
                $fail("Tier {$tierNum} rate must be 0 or greater.");

                return;
            }

            // Empty-string up_to is rejected explicitly: the runtime (ConfigOption::calculateTieredDelta)
            // only treats null/missing as unlimited, while (float) '' coerces to 0 — a divergence
            // that would silently mis-price. Force the caller to use null or omit the key.
            if (array_key_exists('up_to', $tier) && $tier['up_to'] === '') {
                $fail("Tier {$tierNum} \"up_to\" cannot be an empty string; use null or omit the key for unlimited.");

                return;
            }

            // up_to is optional (null/missing = unlimited), but only valid as the LAST tier.
            $hasUpTo = array_key_exists('up_to', $tier) && $tier['up_to'] !== null;

            if (! $hasUpTo) {
                if ($index !== $lastIndex) {
                    $fail("Tier {$tierNum} is unlimited (no \"up_to\"), so it must be the last tier.");

                    return;
                }
                continue;
            }

            if (! is_numeric($tier['up_to'])) {
                $fail("Tier {$tierNum} \"up_to\" must be numeric when provided.");

                return;
            }

            $upTo = (float) $tier['up_to'];

            if ($upTo < 0) {
                $fail("Tier {$tierNum} \"up_to\" value ({$upTo}) must be non-negative.");

                return;
            }

            if ($upTo <= $previousUpTo) {
                $fail("Tier {$tierNum} \"up_to\" value ({$upTo}) must be strictly greater than the previous tier's \"up_to\" value ({$previousUpTo}).");

                return;
            }

            $previousUpTo = $upTo;
        }
    }

    private function validateBaseAddon(array $pricing, Closure $fail): void
    {
        if (! is_numeric($pricing['included_units'])) {
            $fail('The included units must be numeric.');

            return;
        }

        if ((float) $pricing['included_units'] < 0) {
            $fail('The included units must be 0 or greater.');

            return;
        }

        if (! is_numeric($pricing['overage_rate'])) {
            $fail('The overage rate must be numeric.');

            return;
        }

        if ((float) $pricing['overage_rate'] < 0) {
            $fail('The overage rate must be 0 or greater.');
        }
    }
}
