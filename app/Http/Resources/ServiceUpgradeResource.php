<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class ServiceUpgradeResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'status',
        'service_id',
        'plan_id',
        'product_id',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'service' => PropertyResource::class,
        'invoice' => InvoiceResource::class,
    ];
}
