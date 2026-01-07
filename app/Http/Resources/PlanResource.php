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
            // Merge existing standard relationships if needed,
            // or just define them all here.
            'products' => fn () => ProductResource::collection($this->products),

            // Define your custom calculated relationship
            'price' => fn () => new PriceResource($this->price()),
        ];
    }
}
