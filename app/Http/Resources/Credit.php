<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Credit extends JsonApiResource
{
    public $attributes = [
        'id',
        'currency_code',
        'amount',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'user' => User::class,
    ];
}
