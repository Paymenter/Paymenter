<?php

namespace App\Http\Requests\Api\Admin\Products;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetProductRequest extends AdminApiRequest
{
    protected $permission = 'products.view';
}
