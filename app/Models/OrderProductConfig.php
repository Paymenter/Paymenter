<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_product_id',
        'config_option_id',
        'config_value_id',
    ];
}
