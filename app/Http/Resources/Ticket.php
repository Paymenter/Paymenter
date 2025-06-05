<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Ticket extends JsonApiResource
{
    public $attributes = [
        'id',
        'subject',
        'status',
        'priority',
        'department',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'messages' => TicketMessage::class,
        'user' => User::class,
        'assigned_to' => User::class,
    ];
}