<?php

namespace Paymenter\Extensions\Others\Affiliates\Http\Requests;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetAffiliateRequest extends AdminApiRequest
{
    protected $permission = 'affiliates.view';
}
