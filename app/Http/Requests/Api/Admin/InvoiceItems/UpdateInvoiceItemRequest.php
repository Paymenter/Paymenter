<?php

namespace App\Http\Requests\Api\Admin\InvoiceItems;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class UpdateInvoiceItemRequest extends AdminApiRequest
{
    protected $permission = 'invoice_items.update';

    public function rules(): array
    {
        return [
            'description' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer|min:1',
            'price' => 'sometimes|numeric|min:0',
            'reference_type' => 'sometimes|string|max:100',
            'reference_id' => [
                'sometimes',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($this->input('reference_type') && !$value) {
                        $fail('The reference_id field is required when reference_type is provided.');
                    }
                    if ($this->input('reference_type') && $value) {
                        // Check if the reference_type is a valid class
                        if (!class_exists($this->input('reference_type'))) {
                            $fail('The reference_type must be a valid class name.');
                        } else {
                            // Check if the reference_id exists in the specified reference_type table
                            $modelClass = $this->input('reference_type');
                            if (!app($modelClass)->where('id', $value)->exists()) {
                                $fail('The selected reference_id is invalid for the given reference_type.');
                            }
                        }
                    }
                },
            ],
        ];
    }
}
