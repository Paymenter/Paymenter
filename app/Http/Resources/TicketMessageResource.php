<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class TicketMessageResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'message',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'user' => UserResource::class,
        'ticket' => TicketResource::class,
        'attachments' => TicketAttachmentResource::class,
    ];
}
