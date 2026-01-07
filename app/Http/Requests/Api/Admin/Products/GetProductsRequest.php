<?php

namespace App\Http\Requests\Api\Admin\Products;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetProductsRequest extends AdminApiRequest
{
    protected $permission = 'products.view';
}
