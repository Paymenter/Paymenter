<?php

namespace App\Http\Requests\Api\Admin\Products;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class DeleteProductRequest extends AdminApiRequest
{
    protected $permission = 'products.delete';
}
