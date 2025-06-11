<?php

namespace Paymenter\Extensions\Others\Affiliates\Http\Resources;

use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin \App\Models\AffiliateOrder */
class AffiliateOrderResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'order_id',
    ];
}
