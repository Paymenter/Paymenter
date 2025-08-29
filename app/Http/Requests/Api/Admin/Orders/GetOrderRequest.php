<?php

namespace App\Http\Requests\Api\Admin\Orders;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetOrderRequest extends AdminApiRequest
{
    protected $permission = 'orders.view';
}
