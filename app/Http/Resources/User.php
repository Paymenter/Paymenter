<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin \App\Models\User */
class User extends JsonApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }

    public function toRelationships(Request $request): array
    {
        return [
            'properties' => fn() => Property::collection($this->properties),
            'orders' => fn() => Order::collection($this->orders),
            'services' => fn() => Service::collection($this->services),
            'invoices' => fn() => Invoice::collection($this->invoices),
            'tickets' => fn() => Ticket::collection($this->tickets),
            'credits' => fn() => Credit::collection($this->credits),
        ];
    }
}
