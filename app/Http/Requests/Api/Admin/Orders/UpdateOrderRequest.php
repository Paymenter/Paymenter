<?php

namespace App\Http\Requests\Api\Admin\Orders;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class UpdateOrderRequest extends AdminApiRequest
{
    protected $permission = 'orders.update';

    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|required|exists:users,id',
            /**
             * @example USD
             */
            'currency_code' => 'sometimes|required|string|exists:currencies,code',
        ];
    }
}
