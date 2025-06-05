<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class TicketMessage extends JsonApiResource
{
    public $attributes = [
        'id',
        'message',
        'updated_at',
        'created_at',
    ];
    
    public $relationships = [
        'user' => User::class,
        'ticket' => Ticket::class,
    ];
}
