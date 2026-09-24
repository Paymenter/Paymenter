<?php

namespace App\Http\Requests\Api\Admin\Invoices;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use App\Models\Invoice;

class UpdateInvoiceRequest extends AdminApiRequest
{
    protected $permission = 'invoices.update';

    public function authorize(): bool
    {
        if (!parent::authorize()) {
            return false;
        }

        $invoice = $this->route('invoice');

        if (!$invoice instanceof Invoice || !config('settings.immutable_invoices_enabled', false)) {
            return true;
        }

        if ($this->invoiceCreatedBeforeImmutableUpdate($invoice)) {
            return true;
        }

        return $invoice->status === Invoice::STATUS_DRAFT;
    }

    private function invoiceCreatedBeforeImmutableUpdate(Invoice $invoice): bool
    {
        $lockBeforeEnabled = config('settings.immutable_invoices_lock_before', false);
        $lockDate = config('settings.immutable_invoices_lock_date');

        return $lockBeforeEnabled && $lockDate && $invoice->created_at->isBefore($lockDate);
    }

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
