<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Coupon extends JsonApiResource
{
    public $attributes = [
        'id',
        'code',
        'type',
        'recurring',
        'value',
        'max_uses',
        'max_uses_per_user',
        'starts_at',
        'expires_at',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'services' => Service::class,
    ];
}
