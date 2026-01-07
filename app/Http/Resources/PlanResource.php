<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;
use App\Models\Plan; // Import your Plan model

class PlanResource extends JsonApiResource
{
    public $attributes = [
        'name',
        'type',
        'billing_period',
        'billing_unit',
        'sort',
    ];

    // Remove the $relationships property for 'price'
    // or keep only standard relationships here.
    public $relationships = [
        'products' => ProductResource::class,
    ];

    /**
     * Define the relationships.
     */
    public function toRelationships($request): array
    {
        return [
            'products' => fn () => ProductResource::collection($this->products),
            'price' => fn () => $this->price() ? new PriceResource($this->price()) : null,
        ];
    }
}
