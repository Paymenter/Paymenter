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

            'price' => fn () => $this->price()
                ? new PriceResource($this->price())
                : null,
        ];
    }

    public function toAttributes($request): array
    {
        // Calculate the price object safely
        $priceObj = $this->price();

        return [
            'name'           => $this->name,
            'type'           => $this->type,
            'billing_period' => $this->billing_period,
            'billing_unit'   => $this->billing_unit,
            'sort'           => $this->sort,

            // Add the price details directly here
            'price_details'  => $priceObj ? [
                'price'     => $priceObj->price->price ?? null, // Access the inner value
                'setup_fee' => $priceObj->setup_fee,
                'currency'  => $priceObj->currency,
            ] : null,
        ];
    }
}
