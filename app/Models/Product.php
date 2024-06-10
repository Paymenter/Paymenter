<?php

namespace App\Models;

use App\Classes\Price;
use App\Models\Traits\Priceable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory;
    use Priceable;

    protected $guarded = [];

    public $with = ['plans'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'category_id',
        // Either disabled = no quantity, separated = allow multiple products but separate products on checkout, combined = allow multiple products but combine products on checkout
        'allow_quantity'
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
        return $this->belongsToMany(Option::class)->withPivot('enabled');;
    }

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
            return $plan->prices->when($currency, function ($query) use ($currency) {
                return $query->where('currency_code', $currency);
            })->isNotEmpty();
        });
    }

    /**
     * Get first price of the plan.
     */
    public function price($plan = null)
    {
        $priceAndCurrency = [
            'price' => null,
            'currency' => null,
        ];

        $currency = session('currency', config('settings.default_currency'));

        foreach ($this->availablePlans()->when($plan, function ($query) use ($plan) {
            return $query->where('id', $plan);
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
