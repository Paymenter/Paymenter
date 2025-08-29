<?php

namespace App\Http\Requests\Api\Admin\Invoices;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class CreateInvoiceRequest extends AdminApiRequest
{
    protected $permission = 'invoices.create';

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

    public function prepareForValidation()
    {
        $this->mergeIfMissing([
            'status' => 'pending', // Default status to 'pending' if not provided
        ]);
    }
}
