<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Credit extends JsonApiResource
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
            'currency_code' => $this->currency_code,
            'amount' => $this->amount,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }

    public function toRelationships(Request $request): array
    {
        return [
            'user' => fn() => User::make($this->user),
        ];
    }
}
