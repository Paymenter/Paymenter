<?php

namespace Paymenter\Extensions\Others\Affiliates\Http\Resources;

use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin \App\Models\AffiliateOrder */
class AffiliateOrderResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'order_id',
    ];
}
