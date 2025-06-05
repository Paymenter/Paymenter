<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin \App\Models\Invoice
 */
class InvoiceResource extends JsonApiResource
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
    ];
}
