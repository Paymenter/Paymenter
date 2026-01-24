<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class CurrencyResource extends JsonApiResource
{
    public $attributes = [
        'code',
        'name',
        'prefix',
        'suffix',
        'format',
    ];
}
