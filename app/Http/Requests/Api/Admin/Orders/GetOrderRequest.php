<?php

namespace App\Http\Requests\Api\Admin\Orders;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class GetOrderRequest extends AdminApiRequest
{
    protected $permission = 'orders.view';
}
