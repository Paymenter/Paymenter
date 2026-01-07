<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class ProductResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'name',
        'slug',
        'description',
        'stock',
        'per_user_limit',
        'allow_quantity',
        'email_template',
        'enabled',
        'created_at',
        'updated_at',
        'image',
    ];

    public $relationships = [
        'category' => CategoryResource::class,
        'plans' => PlanResource::class,
    ];
}
