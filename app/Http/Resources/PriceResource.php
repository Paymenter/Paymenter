<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class PriceResource extends JsonApiResource
{
    public $attributes = [
        'price',
        'setup_fee',
        'currency',
        'formatted', // Add this! The class generates formatted strings
    ];

    public $relationships = [];
}
