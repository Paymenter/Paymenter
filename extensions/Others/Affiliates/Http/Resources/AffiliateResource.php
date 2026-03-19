<?php

namespace Paymenter\Extensions\Others\Affiliates\Http\Resources;

use App\Http\Resources\UserResource;
use App\Models\Affiliate;
use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin Affiliate */
class AffiliateResource extends JsonApiResource
{
    public $attributes = [
        'id',
        'code',
        'enabled',
        'visitors',
        'reward',
        'discount',
        'earnings',
        'updated_at',
        'created_at',
    ];

    public $relationships = [
        'user' => UserResource::class,
        'orders' => AffiliateOrderResource::class,
    ];
}
