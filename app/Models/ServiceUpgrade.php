<?php

namespace App\Models;

use App\Classes\Price;
use App\Observers\ServiceUpgradeObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([ServiceUpgradeObserver::class])]
class ServiceUpgrade extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public $guarded = [];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function configs()
    {
        return $this->morphMany(ServiceConfig::class, 'configurable');
    }

    public function calculateProratedTotalFor(Product $product, iterable $configOverrides = []): float
    {
        $this->service->loadMissing(['configs.configValue', 'product', 'plan', 'coupon.products', 'currency', 'invoices']);
        $this->loadMissing(['plan', 'product']);

        $oldConfigMap = $this->service->configs
            ->filter(fn (ServiceConfig $config) => $config->configValue !== null)
            ->mapWithKeys(fn (ServiceConfig $config) => [$config->config_option_id => $config->configValue]);

        [$replacements, $removals] = $this->normalizeConfigOverrides($configOverrides, $oldConfigMap);

        $newConfigMap = $oldConfigMap->merge($replacements)->except($removals);

        $oldTotal = $this->calculateCyclePriceFor($this->service->product, $oldConfigMap->values());
        $newTotal = $this->calculateCyclePriceFor($product, $newConfigMap->values());

        $difference = $this->calculateProratedDifferenceFromTotals($oldTotal, $newTotal);

        return (float) number_format($difference, 2, '.', '');
    }

    public function calculatePrice(): Price
    {
        $this->loadMissing(['service', 'product']);

        $newProduct = $this->product ?? $this->service->product;

        $overrides = $this->configs
            ->filter(fn (ServiceConfig $config) => $config->configValue !== null)
            ->mapWithKeys(fn (ServiceConfig $config) => [$config->config_option_id => $config->configValue]);

        $total = $this->calculateProratedTotalFor($newProduct, $overrides);

        return new Price([
            'price' => $total,
            'currency' => $this->service->currency,
        ]);
    }

    protected function calculateProratedDifferenceFromTotals(float $oldPrice, float $newPrice): float
    {
        $billingPeriodDays = match ($this->service->plan->billing_unit) {
            'day' => $this->service->plan->billing_period,
            'week' => $this->service->plan->billing_period * 7,
            'month' => $this->service->plan->billing_period * 30,
            'year' => $this->service->plan->billing_period * 365,
            default => 0,
        };

        if ($billingPeriodDays <= 0 || !$this->service->expires_at) {
            return $newPrice - $oldPrice;
        }

        $expiresAt = $this->service->expires_at->copy()->startOfDay();
        $now = Carbon::now()->startOfDay();
        $remainingDays = $expiresAt->diffInDays($now, true);
        $remainingDays = min($remainingDays, $billingPeriodDays);

        if ($billingPeriodDays === 0.0 || $billingPeriodDays === 0) {
            return $newPrice - $oldPrice;
        }

        return (($newPrice - $oldPrice) / $billingPeriodDays) * $remainingDays;
    }

    protected function calculateCyclePriceFor(Product $product, iterable $configValues): float
    {
        $plan = $this->resolvePlanForProduct($product);

        if (!$plan) {
            return 0.0;
        }

        $currency = $this->service->currency_code;

        $productPrice = $product->price($plan->id, $plan->billing_period, $plan->billing_unit, $currency);
        $total = $productPrice->available ? (float) $productPrice->price : 0.0;

        foreach ($configValues as $configValue) {
            if (!$configValue) {
                continue;
            }

            $configPrice = $configValue->price(null, $plan->billing_period, $plan->billing_unit, $currency);

            if (!$configPrice->available) {
                continue;
            }

            $total += (float) $configPrice->price;
        }

        if ($this->service->coupon && $this->couponAppliesToCurrentCycle() && $this->couponAppliesToProduct($product)) {
            $discount = (float) $this->service->coupon->calculateDiscount($total);
            $total -= $discount;
        }

        return max((float) number_format($total, 2, '.', ''), 0.0);
    }

    protected function resolvePlanForProduct(Product $product): ?Plan
    {
        $servicePlan = $this->service->plan;

        if ($product->id === $this->service->product_id) {
            return $servicePlan;
        }

        if (($this->relationLoaded('plan') && $this->plan?->priceable_id === $product->id) || ($this->plan && $this->plan->priceable_id === $product->id)) {
            return $this->plan;
        }

        return $product->plans()
            ->where('billing_period', $servicePlan->billing_period)
            ->where('billing_unit', $servicePlan->billing_unit)
            ->first();
    }

    protected function couponAppliesToCurrentCycle(): bool
    {
        $coupon = $this->service->coupon;

        if (!$coupon) {
            return false;
        }

        $recurring = (int) ($coupon->recurring ?? 0);

        if ($recurring === 0) {
            return true;
        }

        $paidInvoices = $this->service->invoices()
            ->where('status', Invoice::STATUS_PAID)
            ->count();

        return $paidInvoices === 0 || $paidInvoices <= $recurring;
    }

    protected function couponAppliesToProduct(Product $product): bool
    {
        $coupon = $this->service->coupon;

        if (!$coupon) {
            return false;
        }

        if (!$coupon->relationLoaded('products')) {
            $coupon->loadMissing('products');
        }

        if ($coupon->products->isEmpty()) {
            return true;
        }

        return $coupon->products->contains('id', $product->id);
    }

    /**
     * @return array{Collection<int, \App\Models\ConfigOption>, array<int|string>}
     */
    protected function normalizeConfigOverrides(iterable $configOverrides, Collection $baseConfigMap): array
    {
        $replacements = collect();
        $removals = [];

        foreach ($configOverrides as $key => $override) {
            $optionId = is_numeric($key) ? (int) $key : $key;

            if ($override instanceof ServiceConfig) {
                if ($override->configValue) {
                    $replacements->put($override->config_option_id, $override->configValue);
                }

                continue;
            }

            if ($override instanceof ConfigOption) {
                if (!$optionId && $override->parent_id) {
                    $optionId = $override->parent_id;
                }

                if ($override->parent_id) {
                    $optionId = $override->parent_id;
                }

                if ($optionId !== null) {
                    $replacements->put($optionId, $override);
                }

                continue;
            }

            if ($override === null && $optionId !== null && $baseConfigMap->has($optionId)) {
                $removals[] = $optionId;
            }
        }

        return [$replacements, $removals];
    }
}
