<?php

namespace App\Http\Requests\Api\Admin\Invoices;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use App\Models\Invoice;

class DeleteInvoiceRequest extends AdminApiRequest
{
    protected $permission = 'invoices.delete';

    public function authorize(): bool
    {
        if (!parent::authorize()) {
            return false;
        }

        $invoice = $this->route('invoice');

        if (!$invoice instanceof Invoice || !config('settings.immutable_invoices_enabled', false)) {
            return true;
        }

        return $invoice->status === Invoice::STATUS_DRAFT;
    }
}
