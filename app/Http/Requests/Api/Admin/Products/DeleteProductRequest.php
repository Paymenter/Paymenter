<?php

namespace App\Http\Requests\Api\Admin\Users;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class DeleteProductRequest extends AdminApiRequest
{
    protected $permission = 'products.delete';
}
