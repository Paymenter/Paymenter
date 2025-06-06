<?php

namespace App\Http\Requests\Api\Admin\Orders;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class DeleteOrderRequest extends AdminApiRequest
{
    protected $permission = 'orders.delete';
}
