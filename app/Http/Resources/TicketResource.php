<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class TicketResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'subject',
        'status',
        'priority',
        'assigned_to',
        'user_id',
        'department',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'messages' => TicketMessageResource::class,
        'user' => UserResource::class,
        'assigned_to' => UserResource::class,
    ];
}
