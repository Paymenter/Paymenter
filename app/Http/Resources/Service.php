<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Service extends JsonApiResource
{
    public $attributes = [
        'id',
        'quantity',
        'price',
        'currency_code',
        'expires_at',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'services' => Property::class,
        'coupon' => Coupon::class,
        'user' => User::class,
        'order' => Order::class,
        'product' => Product::class,
        'invoices' => Invoice::class,
    ];
}
