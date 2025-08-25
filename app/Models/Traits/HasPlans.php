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

        foreach ($this->availablePlans(currency: $currency)->when($plan_id, function ($query) use ($plan_id) {
            return $query->where('id', $plan_id);
        }) as $plan) {
            foreach ($plan->prices->when($currency, function ($query) use ($currency) {
                return $query->where('currency_code', $currency);
            }) as $price) {
                if ($price->price < $priceAndCurrency['price'] || $priceAndCurrency['price'] === null) {
                    $priceAndCurrency['price'] = $price;
                    $priceAndCurrency['currency'] = $price->currency;
                }
            }

            if ($priceAndCurrency['price']) {
                break;
            }
        }

        return new Price($priceAndCurrency, dontShowUnavailablePrice: $this->dontShowUnavailablePrice ?? false);
    }
}
