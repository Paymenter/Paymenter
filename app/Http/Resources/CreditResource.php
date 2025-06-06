<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class CreditResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'currency_code',
        'amount',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'user' => UserResource::class,
    ];
}
