<?php

namespace App\Models;

use App\Models\Traits\HasPlans;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class ConfigOption extends Model implements Auditable
{
    use HasFactory, HasPlans, Traits\Auditable;

    protected $dontShowUnavailablePrice = true;

    protected $fillable = [
        'name',
        'description',
        'env_variable',
        'type',
        'sort',
        'hidden',
        'parent_id',
        'upgradable',
    ];

    /**
     * Get the parent option.
     */
    public function parent()
    {
        return $this->belongsTo(ConfigOption::class, 'parent_id');
    }

    public static ?\App\Models\Product $currentProductContext = null;

    public static function setProductContext($product)
    {
        if ($product instanceof \App\Models\Product) {
            self::$currentProductContext = $product;
        } elseif (is_numeric($product)) {
            self::$currentProductContext = \App\Models\Product::find($product);
        } elseif (is_string($product)) {
            self::$currentProductContext = \App\Models\Product::where('slug', $product)->first();
        } else {
            self::$currentProductContext = null;
        }
    }

    /**
     * Get the options that belong to the parent. (children or options)
     */
    public function children()
    {
        $query = $this->hasMany(ConfigOption::class, 'parent_id')->orderBy('sort');

        if (self::$currentProductContext) {
            $productId = self::$currentProductContext->id;

            $query->where(function ($q) use ($productId) {
                $q->whereNotExists(function ($sub) {
                    $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from('config_option_products')
                        ->whereColumn('config_option_products.config_option_id', 'config_options.id');
                })->orWhereExists(function ($sub) use ($productId) {
                    $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from('config_option_products')
                        ->whereColumn('config_option_products.config_option_id', 'config_options.id')
                        ->where('config_option_products.product_id', $productId);
                });
            });
        }

        return $query;
    }

    /**
     * Get the products that belong to the option.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'config_option_products');
    }

    /**
     * Get the service configs that belong to the option.
     */
    public function serviceConfigs()
    {
        return $this->hasMany(ServiceConfig::class, 'config_option_id');
    }
}
