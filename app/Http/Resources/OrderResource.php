<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class OrderResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'currency_code',
        'user_id',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'services' => ServiceResource::class,
        'user' => UserResource::class,
    ];
}
