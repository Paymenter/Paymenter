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
     * Calculate dynamic slider price for a given value
     * Supports linear, tiered, and base_addon pricing models
     */
    public function calculateDynamicPrice(float $value, int $billingPeriod = 1, string $billingUnit = 'month'): float
    {
        if (! $this->isDynamicSlider()) {
            return 0;
        }

        $pricing = $this->metadata['pricing'] ?? [];
        $model = $pricing['model'] ?? 'linear';

        $monthlyPrice = match ($model) {
            'tiered' => $this->calculateTieredPrice($value, $pricing),
            'base_addon' => $this->calculateBaseAddonPrice($value, $pricing),
            default => $this->calculateLinearPrice($value, $pricing),
        };

        return $monthlyPrice * $this->getBillingMultiplier($billingPeriod, $billingUnit);
    }

    /**
     * Calculate linear pricing: base_price + (displayValue * rate_per_unit)
     */
    private function calculateLinearPrice(float $value, array $pricing): float
    {
        $displayDivisor = $this->metadata['display_divisor'] ?? 1;
        $displayValue = $value / $displayDivisor;

        $basePrice = $pricing['base_price'] ?? 0;
        $ratePerUnit = $pricing['rate_per_unit'] ?? 0;

        return $basePrice + ($displayValue * $ratePerUnit);
    }

    /**
     * Calculate tiered pricing: volume discounts at breakpoints
     * Example: First 4GB at $3/GB, next 12GB at $2.50/GB, rest at $2/GB
     */
    private function calculateTieredPrice(float $value, array $pricing): float
    {
        $displayDivisor = $this->metadata['display_divisor'] ?? 1;
        $remainingUnits = $value / $displayDivisor;

        $total = $pricing['base_price'] ?? 0;
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
     * Calculate base+addon pricing: included units free, then overage rate
     * Example: 4GB included, $2.50/GB for additional
     */
    private function calculateBaseAddonPrice(float $value, array $pricing): float
    {
        $displayDivisor = $this->metadata['display_divisor'] ?? 1;
        $displayValue = $value / $displayDivisor;

        $basePrice = $pricing['base_price'] ?? 0;
        $includedUnits = $pricing['included_units'] ?? 0;
        $overageRate = $pricing['overage_rate'] ?? 0;

        $overageUnits = max(0, $displayValue - $includedUnits);

        return $basePrice + ($overageUnits * $overageRate);
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
