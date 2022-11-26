<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProducts extends Model
{
    use HasFactory;
    protected $table = 'order_products';
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity'
    ];

    public function config()
    {
        return $this->hasMany(OrderProductsConfig::class, 'order_product_id', 'product_id');
    }
}
