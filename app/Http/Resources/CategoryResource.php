<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class CategoryResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'name',
        'parent_id',
        'permissions',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'products' => ProductResource::class,
        'parent' => CategoryResource::class,
        'children' => CategoryResource::class,
    ];
}
