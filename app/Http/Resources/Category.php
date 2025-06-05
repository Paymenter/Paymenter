<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Category extends JsonApiResource
{
    public $attributes = [
        'id',
        'name',
        'permissions',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'products' => Product::class,
    ];
}
