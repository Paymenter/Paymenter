<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class RoleResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'name',
        'permissions',
        'updated_at',
        'created_at',
    ];
}
