<?php

namespace App\Models;

use App\Classes\Price;
use App\Models\Traits\Priceable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable, Priceable;

    protected $guarded = [];

    public $with = ['plans'];

    protected $auditInclude = [
        'name',
        'description',
        'category_id',
        'enabled',
    ];

    /**
     * Get the category of the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the configurable options of the product.
     */
    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class)->withPivot('enabled');
    }

    /**
     * Get the plans (prices) of the product.
     */
    public function plans()
    {
        return $this->morphMany(Plan::class, 'priceable')->orderBy('sort');
    }

    /**
     * Get the extension of the product.
     */
    public function server()
    {
        return $this->belongsTo(Extension::class, 'server_id')->where('type', 'server');
    }

    /**
     * Get the settings of the product.
     */
    public function settings(): MorphMany
    {
        return $this->morphMany(Setting::class, 'settingable');
    }

    /**
     * Get available plans of the product.
     */
    public function availablePlans()
    {
        $currency = session('currency', config('settings.default_currency'));

        return $this->plans->filter(function ($plan) use ($currency) {
            return $plan->prices->when($currency, function ($query) use ($currency) {
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
