<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class InvoiceItem extends JsonApiResource
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
        'services' => Service::class,
        'invoice' => Invoice::class,
    ];
}
