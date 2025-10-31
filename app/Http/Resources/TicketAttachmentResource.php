<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class TicketAttachmentResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'uuid',
        'filename',
        'path',
        'filesize',
        'mime_type',
        'created_at',
        'updated_at',
    ];

    public $relationships = [
        'message' => TicketMessageResource::class,
    ];
}
