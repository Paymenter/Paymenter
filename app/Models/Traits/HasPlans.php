<?php

namespace App\Models\Traits;

use App\Classes\Price;
use App\Models\Plan;

trait HasPlans
{
    /**
     * Get the plans (prices) of the product.
     */
    public function plans()
    {
        return $this->morphMany(Plan::class, 'priceable')->orderBy('sort');
    }

    /**
     * Get available plans of the product.
     */
    public function availablePlans($currency = null)
    {
        $currency = $currency ?? session('currency', config('settings.default_currency'));

        return $this->plans->filter(function ($plan) use ($currency) {
            if ($plan->type === 'free') {
                return true;
            }

            return $plan->prices->when($currency, function ($query) use ($currency) {
                // Or where plan is free
                return $query->where('currency_code', $currency);
            })->isNotEmpty();
        });
    }

    /**
     * Get first price of the plan.
     */
    public function price($plan_id = null, $billing_period = null, $billing_unit = null, $currency = null)
    {
        $priceAndCurrency = [
            'price' => null,
            'currency' => null,
        ];

        // Check for free plan
        if ($this->availablePlans()->where('type', 'free')->isNotEmpty()) {
            return new Price(free: true, dontShowUnavailablePrice: $this->dontShowUnavailablePrice ?? false);
        }

        // If plan_id is not provided, and billing_period is provided, get the first plan with the billing period and time interval
        if (!$plan_id && $billing_period && $billing_unit) {
            $plan = $this->availablePlans()->where('billing_period', $billing_period)->where('billing_unit', $billing_unit)->first();
            $plan_id = $plan->id ?? null;
        }

        $currency = $currency ?? session('currency', config('settings.default_currency'));

        foreach (
            $this->availablePlans(currency: $currency)->when($plan_id, function ($query) use ($plan_id) {
                return $query->where('id', $plan_id);
            }) as $plan
        ) {
            foreach (
                $plan->prices->when($currency, function ($query) use ($currency) {
                    return $query->where('currency_code', $currency);
                }) as $price
            ) {
                if ($price->price < $priceAndCurrency['price'] || $priceAndCurrency['price'] === null) {
                    $priceAndCurrency['price'] = $price;
                    $priceAndCurrency['currency'] = $price->currency;
                }
            }

            if ($priceAndCurrency['price']) {
                break;
            }
        }

        // If this is a Product with configurable options, add the lowest config option price
        if (method_exists($this, 'configOptions') && $this->configOptions()->exists()) {
            $lowestConfigPrice = 0; // Start with 0 as minimum
            
            // Get billing period and unit from the plan if not provided
            $currentPlan = null;
            if ($priceAndCurrency['price']) {
                // Find the plan associated with the current price
                foreach ($this->availablePlans(currency: $currency) as $p) {
                    foreach ($p->prices as $planPrice) {
                        if ($planPrice->id === $priceAndCurrency['price']->id) {
                            $currentPlan = $p;
                            break 2;
                        }
                    }
                }
                
                $effectiveBillingPeriod = $billing_period ?? $currentPlan?->billing_period;
                $effectiveBillingUnit = $billing_unit ?? $currentPlan?->billing_unit;
                
                // For each config option, find the minimum required price
                foreach ($this->configOptions as $configOption) {
                    if ($configOption->children()->exists()) {
                        $optionMinPrice = null;
                        
                        // Find the cheapest option within this config category
                        foreach ($configOption->children as $child) {
                            $childPrice = $child->price(null, $effectiveBillingPeriod, $effectiveBillingUnit, $currency);
                            
                            if ($childPrice->available) {
                                $childPriceValue = $childPrice->price ?? 0;
                                
                                if ($optionMinPrice === null || $childPriceValue < $optionMinPrice) {
                                    $optionMinPrice = $childPriceValue;
                                }
                            }
                        }
                        
                        // Add the minimum price for this config option to the total
                        if ($optionMinPrice !== null) {
                            $lowestConfigPrice += $optionMinPrice;
                        }
                    }
                }
                
                // Add the lowest total config options price to the base product price
                if ($lowestConfigPrice > 0) {
                    $priceAndCurrency['price']->price += $lowestConfigPrice;
                }
            }
        }

        return new Price($priceAndCurrency, dontShowUnavailablePrice: $this->dontShowUnavailablePrice ?? false);
    }
}
