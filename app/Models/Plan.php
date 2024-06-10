<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Classes\Price as PriceClass;

class Plan extends Model
{
    use HasFactory;

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
