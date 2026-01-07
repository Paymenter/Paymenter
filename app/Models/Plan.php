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
        // 1. Logic for free plans
        if ($this->type === 'free') {
            $currency = Currency::where('code', session('currency', config('settings.default_currency')))->first();
            return new PriceClass(['currency' => $currency], free: true);
        }

        // 2. Determine Currency
        $currencyCode = session('currency', config('settings.default_currency'));

        // 3. Find the Price Model
        // Eager load currency to avoid extra queries and ensure format is available
        $priceModel = $this->prices()
            ->with('currency') // Make sure we have the currency relation loaded!
            ->where('currency_code', $currencyCode)
            ->first();

        // Fallback if specific currency not found
        if (!$priceModel) {
            $priceModel = $this->prices()->with('currency')->first();
        }

        // 4. Return null if no price exists at all
        if (!$priceModel) {
            return null;
        }

        // 5. Construct PriceClass Correctly
        // The class expects an object with 'price', 'setup_fee', and 'currency' (object)
        return new PriceClass((object) [
            'price'     => $priceModel->price,      // The float value (e.g., 10.00)
            'setup_fee' => $priceModel->setup_fee,  // The float value
            'currency'  => $priceModel->currency,   // The Currency MODEL/Object
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
