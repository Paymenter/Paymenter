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

            'price' => function () {
                // 1. Force fetch prices directly from DB for this plan
                $dbPrice = Price::where('plan_id', $this->id)->first();

                // 2. If no price exists in DB, return null immediately
                if (!$dbPrice) {
                    return null;
                }

                // 3. Construct the fake object expected by PriceResource
                // We mock the object structure so PriceResource doesn't crash
                $fakePriceObject = (object) [
                    'price' => $dbPrice, // The Price Model
                    'setup_fee' => $dbPrice->setup_fee,
                    'currency' => $dbPrice->currency,
                ];

                return new PriceResource($fakePriceObject);
            },
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
