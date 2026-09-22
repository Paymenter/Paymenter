<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class ServiceResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'quantity',
        'price',
        'status',
        'currency_code',
        'expires_at',
        'suspend_hold_until',
        'updated_at',
        'created_at',
        'label',
    ];

    public $relationships = [
        'coupon' => CouponResource::class,
        'user' => UserResource::class,
        'order' => OrderResource::class,
        'product' => ProductResource::class,
        'invoices' => InvoiceResource::class,
        'properties' => PropertyResource::class,
    ];
}
