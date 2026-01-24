<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class PriceResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'price',
        'setup_fee',
        'currency_code',
    ];

    public $relationships = [
        'currency' => CurrencyResource::class,
    ];
}
