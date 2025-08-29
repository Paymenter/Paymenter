<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class CategoryResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'name',
        'permissions',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'products' => ProductResource::class,
    ];
}
