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
        'server_id',
        'stock',
        'stock_enabled',
        'allow_quantity',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function server()
    {
        return $this->belongsTo(Extension::class, 'server_id');
    }

    public function settings()
    {
        return $this->hasMany(ProductSetting::class, 'product_id');
    }

    public function prices()
    {
        return $this->hasOne(ProductPrice::class, 'product_id');
    }

    public function price($type = null)
    {
        $prices = $this->prices()->get()->first();
        if ($prices->type == 'one-time') {
            if($type == 'setup')
                return $prices->monthly_setup;
            else
                return $prices->monthly;
        } else if ($prices->type == 'free') {
            return 0;
        } else {
            if($type == 'setup')
                return $prices->{$prices->type . '_setup'};
            else if ($type)
                return $prices->{$type};
            else
                return $prices->monthly ?? $prices->quarterly ?? $prices->semi_annually ?? $prices->annually ?? $prices->biennially ?? $prices->triennially;
            }
    }
}
