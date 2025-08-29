<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class ProductResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'name',
        'permissions',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'category' => CategoryResource::class,
    ];
}
