<?php

namespace App\Http\Requests\Api\Admin\InvoiceItems;

class DeleteInvoiceItemRequest extends InvoiceItemRequest
{
    protected $permission = 'invoice_items.delete';

    public function authorize(): bool
    {
        return $this->authorizeInvoiceItem();
    }
}
