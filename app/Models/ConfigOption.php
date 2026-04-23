<?php

namespace App\Models;

use App\Models\Traits\HasPlans;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class ConfigOption extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory, HasPlans;

    protected $dontShowUnavailablePrice = true;

    protected $fillable = [
        'name',
        'env_variable',
        'type',
        'sort',
        'hidden',
        'parent_id',
        'upgradable',
        'metadata',
    ];

    protected $casts = [
        'hidden' => 'boolean',
        'upgradable' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Check if this is a dynamic slider type
     */
    public function isDynamicSlider(): bool
    {
        return $this->type === 'dynamic_slider';
    }

    /**
     * Get a metadata value with a default
     */
    public function getMetadata(string $key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    /**
     * Calculate the marginal (delta) price for a dynamic slider value.
     * Does NOT include the shared per-product base_price — that is handled
     * at the plan level via Plan::dynamicSliderBasePrice().
     */
    public function calculateDynamicPriceDelta(float $value, int $billingPeriod = 1, string $billingUnit = 'month'): float
    {
        if (! $this->isDynamicSlider()) {
            return 0;
        }

        $pricing = $this->metadata['pricing'] ?? [];
        $model = $pricing['model'] ?? 'linear';

        $monthlyDelta = match ($model) {
            'linear'     => $this->calculateLinearDelta($value, $pricing),
            'tiered'     => $this->calculateTieredDelta($value, $pricing),
            'base_addon' => $this->calculateBaseAddonDelta($value, $pricing),
            default      => throw new \InvalidArgumentException("Unknown dynamic_slider pricing model: ".var_export($model, true)),
        };

        return $monthlyDelta * $this->getBillingMultiplier($billingPeriod, $billingUnit);
    }

    /**
     * @deprecated Use calculateDynamicPriceDelta() for the marginal charge and add
     *             plan->dynamicSliderBasePrice() once per product for the shared base.
     *             This alias returns delta + sharedBase so existing callers see the
     *             same total they always did (base_price counted once, not per-slider).
     */
    public function calculateDynamicPrice(float $value, int $billingPeriod = 1, string $billingUnit = 'month'): float
    {
        $pricing = $this->metadata['pricing'] ?? [];
        $sharedBase = (float) ($pricing['base_price'] ?? 0);
        $multiplier = $this->getBillingMultiplier($billingPeriod, $billingUnit);

        return $this->calculateDynamicPriceDelta($value, $billingPeriod, $billingUnit)
            + ($sharedBase * $multiplier);
    }

    /**
     * Calculate linear marginal price: (displayValue * rate_per_unit) — no base_price.
     */
    private function calculateLinearDelta(float $value, array $pricing): float
    {
        $displayDivisor = $this->metadata['display_divisor'] ?? 1;
        $displayValue = $value / $displayDivisor;
        $ratePerUnit = $pricing['rate_per_unit'] ?? 0;

        return $displayValue * $ratePerUnit;
    }

    /**
     * Calculate tiered marginal price — no base_price.
     */
    private function calculateTieredDelta(float $value, array $pricing): float
    {
        $displayDivisor = $this->metadata['display_divisor'] ?? 1;
        $remainingUnits = $value / $displayDivisor;

        $total = 0.0;
        $previousLimit = 0;

        foreach ($pricing['tiers'] ?? [] as $tier) {
            if ($remainingUnits <= 0) {
                break;
            }

            $tierLimit = $tier['up_to'] ?? PHP_INT_MAX;
            $tierSize = $tierLimit - $previousLimit;
            $unitsInTier = min($remainingUnits, $tierSize);

            $total += $unitsInTier * ($tier['rate'] ?? 0);
            $remainingUnits -= $unitsInTier;
            $previousLimit = $tierLimit;
        }

        return $total;
    }

    /**
     * Calculate base+addon marginal price — no base_price.
     */
    private function calculateBaseAddonDelta(float $value, array $pricing): float
    {
        $displayDivisor = $this->metadata['display_divisor'] ?? 1;
        $displayValue = $value / $displayDivisor;

        $includedUnits = $pricing['included_units'] ?? 0;
        $overageRate = $pricing['overage_rate'] ?? 0;

        $overageUnits = max(0, $displayValue - $includedUnits);

        return $overageUnits * $overageRate;
    }

    /**
     * Get billing period multiplier (assumes rates are monthly)
     */
    private function getBillingMultiplier(int $billingPeriod, string $billingUnit): float
    {
        return match ($billingUnit) {
            'day' => $billingPeriod / 30,
            'week' => $billingPeriod / 4,
            'month' => $billingPeriod,
            'year' => $billingPeriod * 12,
            default => $billingPeriod,
        };
    }

    /**
     * Format value for display (e.g., 4096 MB -> "4 GB")
     */
    public function formatValueForDisplay(float $value): string
    {
        $metadata = $this->metadata ?? [];
        $displayDivisor = $metadata['display_divisor'] ?? 1;
        $displayUnit = $metadata['display_unit'] ?? $metadata['unit'] ?? '';
        $resourceType = $metadata['resource_type'] ?? 'custom';

        // Handle CPU percentage special case
        if ($resourceType === 'cpu') {
            $cores = $value / 100;

            return $cores.' '.($cores == 1 ? 'core' : 'cores');
        }

        $displayValue = $value / $displayDivisor;

        // Format nicely - remove decimals if whole number
        $formatted = $displayValue == (int) $displayValue
            ? (int) $displayValue
            : number_format($displayValue, 1);

        return $formatted.' '.$displayUnit;
    }

    /**
     * Get the parent option.
     */
    public function parent()
    {
        return $this->belongsTo(ConfigOption::class, 'parent_id');
    }

    /**
     * Get the options that belong to the parent. (children or options)
     */
    public function children()
    {
        return $this->hasMany(ConfigOption::class, 'parent_id')->orderBy('sort');
    }

    /**
     * Get the products that belong to the option.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'config_option_products');
    }

    /**
     * Get the service configs that belong to the option.
     */
    public function serviceConfigs()
    {
        return $this->hasMany(ServiceConfig::class, 'config_option_id');
    }
}
