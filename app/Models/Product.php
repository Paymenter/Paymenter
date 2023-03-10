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
        'price',
        'category_id',
        'image',
        'server_id',
        'stock',
        'stock_enabled',
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
}
