<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Order extends JsonApiResource
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
            'curreny_code' => $this->currency_code,
        ];
    }

    public function toRelationships(Request $request): array
    {
        return [
            'services' => fn() => Service::collection($this->services),
        ];
    }
}
