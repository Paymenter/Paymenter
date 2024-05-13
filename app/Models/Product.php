<?php

namespace App\Models;

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

    protected $fillable = [
        'name',
        'description',
        'image',
        'category_id',
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
     * Get first price of the plan.
     */
    public function price()
    {
        $price = 0;

        foreach ($this->plans as $plan) {
            foreach ($plan->prices as $price) {
                if ($price->price < $price) {
                    $price = $price->price;
                    break 2;
                }
            }
        }

        return $price;
    }
}
