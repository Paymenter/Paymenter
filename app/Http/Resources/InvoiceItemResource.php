<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class InvoiceItemResource extends JsonApiResource
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
        'services' => ServiceResource::class,
        'invoice' => InvoiceResource::class,
    ];
}
