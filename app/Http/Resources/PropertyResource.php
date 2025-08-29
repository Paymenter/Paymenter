<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class PropertyResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'name',
        'key',
        'value',
        'updated_at',
        'created_at',
    ];
}
