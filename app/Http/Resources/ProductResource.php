<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class ProductResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'name',
        'description',
        'image',
        'slug',
        'stock',
        'per_user_limit',
        'sort',
        'allow_quantity',
        'email_template',
        'hidden',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'category' => CategoryResource::class,
        'plans' => PlanResource::class,
    ];
}
