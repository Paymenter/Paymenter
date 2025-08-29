<?php

namespace App\Http\Requests\Api\Admin\Orders;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class CreateOrderRequest extends AdminApiRequest
{
    protected $permission = 'orders.create';

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            /**
             * @example USD
             */
            'currency_code' => 'required|string|exists:currencies,code',
        ];
    }
}
