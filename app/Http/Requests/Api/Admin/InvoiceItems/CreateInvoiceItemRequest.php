<?php

namespace App\Http\Requests\Api\Admin\InvoiceItems;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class CreateInvoiceItemRequest extends AdminApiRequest
{
    protected $permission = 'invoice_items.create';

    public function rules(): array
    {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => [
                'nullable',
                'integer',
                // Ensure reference_id is provided if reference_type is provided
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

    public function prepareForValidation()
    {
        $this->mergeIfMissing([
            'status' => 'pending', // Default status to 'pending' if not provided
        ]);
    }
}
