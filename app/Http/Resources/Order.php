<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Order extends JsonApiResource
{
    public $attributes = [
        'id',
        'currency_code',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'services' => Service::class,
    ];
}
