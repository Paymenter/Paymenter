<?php

namespace App\Http\Requests\Api\Admin\Invoices;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class UpdateInvoiceRequest extends AdminApiRequest
{
    protected $permission = 'invoices.update';

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            /**
             * @example USD
             */
            'currency_code' => 'required|string|exists:currencies,code',
            'due_at' => 'nullable|date',
            /**
             * @default pending
             */
            'status' => 'required|string|in:pending,paid,cancelled', // Status can be one of these values
        ];
    }
}
