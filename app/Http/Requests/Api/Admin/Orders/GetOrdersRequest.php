<?php

namespace App\Http\Requests\Api\Admin\Orders;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetOrdersRequest extends AdminApiRequest
{
    protected $permission = 'orders.view';
}
