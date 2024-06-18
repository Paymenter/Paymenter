<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Classes\Price as PriceClass;
use OwenIt\Auditing\Contracts\Auditable;

class Plan extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'billing_period',
        'billing_unit',
    ];

    public $with = ['prices'];

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
        $currency = session('currency', config('settings.default_currency'));;
        $price = $this->prices->where('currency_code', $currency)->first();
        
        return new PriceClass((object) [
            'price' => $price,
            'setup_fee' => $price->setup_fee,
            'currency' => $price->currency,
        ]);
    }
}
