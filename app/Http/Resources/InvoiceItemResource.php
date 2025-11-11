<?php

namespace App\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

class InvoiceItemResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'description',
        'quantity',
        'price',
        'reference_type',
        'reference_id',
        'updated_at',
        'created_at',
    ];

    public function toRelationships($request)
    {
        return [
            'reference' => function () {
                if ($this->reference_type === 'App\Models\Credit') {
                    return new CreditResource($this->reference);
                } elseif ($this->reference_type === 'App\Models\ServiceUpgrade') {
                    return new ServiceUpgradeResource($this->reference);
                }

                return new ServiceResource($this->reference);
            },
            'invoice' => $this->whenLoaded('invoice', function () {
                return new InvoiceResource($this->invoice);
            }),
        ];
    }
}
