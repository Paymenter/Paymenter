<?php

namespace App\Http\Resources;

use App\Models\Invoice;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin Invoice
 */
class InvoiceResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'status',
        'currency_code',
        'due_at',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'user' => UserResource::class,
        'items' => InvoiceItemResource::class,
    ];
}
