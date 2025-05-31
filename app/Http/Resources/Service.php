<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Service extends JsonApiResource
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
            'quantity' => $this->quantity,
            'price' => $this->price,
            'currency_code' => $this->currency_code,
            'expires_at' => $this->expires_at,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }

    public function toRelationships(Request $request): array
    {
        return [
            'services' => fn() => Property::collection($this->properties),
            'coupon' => fn() => Coupon::make($this->coupon),
        ];
    }
}
