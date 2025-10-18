<?php

namespace App\Models;

use App\Observers\CategoryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy(CategoryObserver::class)]
class Category extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    protected $guarded = [];

    /**
     * Get the products of the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the parent category of the category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the children categories of the category.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function getMinPriceAttribute()
    {
        $minPrice = null;
        $minPriceProduct = null;

        foreach ($this->products as $product) {
            foreach ($product->plans as $plan) {
                foreach ($plan->prices as $price) {
                    if ($price->price && $price->price > 0) {
                        if (is_null($minPrice) || $price->price < $minPrice) {
                            $minPrice = $price->price;
                            $minPriceProduct = $product;
                        }
                    }
                }
            }
        }
        if ($minPriceProduct) {
            return $minPriceProduct->price();
        }

        return null;
    }

    protected $auditExclude = [
        'remember_token',
    ];
}
