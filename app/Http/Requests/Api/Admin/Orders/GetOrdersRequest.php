<?php

namespace App\Http\Requests\Api\Admin\Orders;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class GetOrdersRequest extends AdminApiRequest
{
    protected $permission = 'orders.view';
}
