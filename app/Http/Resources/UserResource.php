<?php

namespace App\Http\Resources;

use App\Models\User;
use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin User */
class UserResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'properties' => PropertyResource::class,
        'orders' => OrderResource::class,
        'services' => ServiceResource::class,
        'invoices' => InvoiceResource::class,
        'tickets' => TicketResource::class,
        'credits' => CreditResource::class,
        'role' => RoleResource::class,
    ];

    public function toMeta($request): array
    {
        // Is properties loaded?
        $meta = [];
        if ($this->resource->relationLoaded('properties')) {
            $meta['properties'] = $this->resource->properties->mapWithKeys(function ($property) {
                return [$property->key => $property->value];
            });
        }

        return $meta;
    }
}
