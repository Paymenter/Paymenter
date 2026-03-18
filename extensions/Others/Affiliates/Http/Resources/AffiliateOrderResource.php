<?php

namespace Paymenter\Extensions\Others\Affiliates\Http\Resources;

use App\Models\AffiliateOrder;
use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin AffiliateOrder */
class AffiliateOrderResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'order_id',
    ];
}
