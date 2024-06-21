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
    public function availablePlans()
    {
        $currency = session('currency', config('settings.default_currency'));

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
    public function price($plan_id = null)
    {
        $priceAndCurrency = [
            'price' => null,
            'currency' => null,
        ];

        // Check for free plan
        if ($this->availablePlans()->where('type', 'free')->isNotEmpty()) {
            return new Price(free: true);
        }

        $currency = session('currency', config('settings.default_currency'));

        foreach ($this->availablePlans()->when($plan_id, function ($query) use ($plan_id) {
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
        }

        return new Price((object) $priceAndCurrency);
    }
}
