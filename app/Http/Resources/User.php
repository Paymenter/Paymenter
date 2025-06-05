<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin \App\Models\User */
class User extends JsonApiResource
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
        'properties' => Property::class,
        'orders' => Order::class,
        'services' => Service::class,
        'invoices' => Invoice::class,
        'tickets' => Ticket::class,
        'credits' => Credit::class,
        'role' => Role::class,
    ];
}
