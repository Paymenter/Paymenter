<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductsConfig extends Model
{
    use HasFactory;
    protected $table = 'order_products_config';
    protected $fillable = [
        'order_product_id',
        'key',
        'value'
    ];

    public function product()
    {
        return $this->belongsTo(OrderProducts::class, 'order_id', 'product_id');
    }

}
