<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Property extends JsonApiResource
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
