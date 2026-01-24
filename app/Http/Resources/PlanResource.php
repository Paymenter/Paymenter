<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class PlanResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'name',
        'type',
        'billing_period',
        'billing_unit',
        'sort',
    ];

    public $relationships = [
        'prices' => PriceResource::class,
    ];
}
