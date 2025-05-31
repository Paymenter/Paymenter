<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Coupon extends JsonApiResource
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
            'code' => $this->code,
            'type' => $this->type,
            'recurring' => $this->recurring,
            'value' => $this->value,
            'max_uses' => $this->max_uses,
            'max_uses_per_user' => $this->max_uses_per_user,
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expires_at,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }

    public function toRelationships(Request $request): array
    {
        return [
            'services' => fn() => Service::collection($this->services),
        ];
    }
}
