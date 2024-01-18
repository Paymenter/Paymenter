<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'image',
        'extension_id',
        'stock',
        'stock_enabled',
        'allow_quantity',
        'order',
        'limit',
        'hidden',
        'upgrade_configurable_options',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function extension()
    {
        return $this->belongsTo(Extension::class, 'extension_id');
    }

    public function settings()
    {
        return $this->hasMany(ProductSetting::class, 'product_id');
    }

    public function prices()
    {
        return $this->hasOne(ProductPrice::class);
    }

    public function upgrades()
    {
        return $this->hasMany(ProductUpgrade::class, 'product_id');
    }

    public function price($type = null)
    {
        $prices = $this->prices;

        if ($prices->type == 'one-time') {
            if ($type == 'setup')
                return $prices->monthly_setup;
            else
                return $prices->monthly;
        } else if ($prices->type == 'free') {
            return 0;
        } else {
            if ($type == 'setup')
                return $prices->{$prices->type . '_setup'};
            else if ($type)
                return $prices->{$type};
            else
                return $prices->monthly ?? $prices->quarterly ?? $prices->semi_annually ?? $prices->annually ?? $prices->biennially ?? $prices->triennially;
        }
    }

    public function configurableGroups()
    {
        // Check all groups products array
        $groups = ConfigurableGroup::all();
        $configurableGroups = [];
        foreach ($groups as $group) {
            $products = $group->products;
            if (in_array($this->id, $products)) {
                $configurableGroups[] = $group;
            }
        }
        return $configurableGroups;
    }
}
