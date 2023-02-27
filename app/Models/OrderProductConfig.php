<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderProductConfig extends Model
{
    use HasFactory;
    protected $table = 'order_products_config';
    protected $fillable = [
        'order_product_id',
        'key',
        'value',
    ];

    public function product()
    {
        return $this->belongsTo(OrderProduct::class, 'order_id', 'product_id');
    }
}
