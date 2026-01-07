<?php

namespace App\Models;

use App\Classes\Price as PriceClass;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class Plan extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'billing_period',
        'billing_unit',
        'sort',
    ];

    protected $casts = [
        'billing_period' => 'integer',
    ];

    /**
     * Get the available prices of the plan.
     */
    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    /**
     * Get the priceable model of the plan.
     */
    public function priceable()
    {
        return $this->morphTo();
    }

    /**
     * Get the price of the plan.
     */
    public function price()
    {
        if ($this->type === 'free') {
            return new PriceClass([
                'currency' => Currency::find(session('currency', config('settings.default_currency')))
            ], free: true);
        }

        $currency = session('currency', config('settings.default_currency'));

        // FIX: Use the method `prices()` to get a Query Builder, then get()
        // This ensures we actually hit the DB if the relationship wasn't eager loaded
        $allPrices = $this->relationLoaded('prices') ? $this->prices : $this->prices()->get();

        $price = $allPrices->where('currency_code', $currency)->first();

        if (!$price) {
            $price = $allPrices->first();
        }

        if (!$price) {
            return null;
        }

        return new PriceClass((object) [
            'price' => $price, // CAREFUL: Ensure PriceClass handles the raw model
            'setup_fee' => $price->setup_fee,
            'currency' => $price->currency,
        ]);
    }

    // Time between billing periods
    public function billingDuration(): Attribute
    {
        if ($this->type === 'free' || $this->type == 'one-time') {
            return Attribute::make(get: fn () => 0);
        }
        $diffInDays = match ($this->billing_unit) {
            'day' => 1,
            'week' => 7,
            'month' => 30,
            'year' => 365,
        };

        return Attribute::make(
            get: fn () => $diffInDays * $this->billing_period
        );
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
