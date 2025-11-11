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
            return new PriceClass(['currency' => Currency::find(session('currency', config('settings.default_currency')))], free: true);
        }
        $currency = session('currency', config('settings.default_currency'));
        $price = $this->prices->where('currency_code', $currency)->first();

        return new PriceClass((object) [
            'price' => $price,
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
