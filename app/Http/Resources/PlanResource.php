<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class PlanResource extends JsonApiResource
{
    public $attributes = [
        'name',
        'type',
        'billing_period',
        'billing_unit',
        'sort',
    ];

    public $relationships = [
        'products' => ProductResource::class,
    ];
}
