<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class ServiceResource extends JsonApiResource
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
        'services' => PropertyResource::class,
        'coupon' => CouponResource::class,
        'user' => UserResource::class,
        'order' => OrderResource::class,
        'product' => ProductResource::class,
        'invoices' => InvoiceResource::class,
    ];
}
