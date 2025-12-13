<?php

namespace App\Http\Requests\Api\Admin\Users;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetProductsRequest extends AdminApiRequest
{
    protected $permission = 'products.view';
}
