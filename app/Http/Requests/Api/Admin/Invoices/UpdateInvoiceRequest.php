<?php

namespace App\Http\Requests\Api\Admin\Invoices;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class UpdateInvoiceRequest extends AdminApiRequest
{
    protected $permission = 'invoices.update';

    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|required|exists:users,id',
            /**
             * @example USD
             */
            'currency_code' => 'sometimes|required|string|exists:currencies,code',
            'due_at' => 'sometimes|nullable|date',
            /**
             * @default pending
             */
            'status' => 'sometimes|required|string|in:pending,paid,cancelled', // Status can be one of these values
        ];
    }
}
