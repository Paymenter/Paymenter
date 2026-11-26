<?php

namespace App\Http\Requests\Api\Admin\InvoiceItems;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;

abstract class InvoiceItemRequest extends AdminApiRequest
{
    protected function authorizeInvoice(?Invoice $invoice): bool
    {
        if (!parent::authorize()) {
            return false;
        }

        if (!$invoice || !config('settings.immutable_invoices_enabled', false)) {
            return true;
        }

        return $invoice->status === Invoice::STATUS_DRAFT;
    }

    protected function authorizeInvoiceItem(): bool
    {
        $invoiceItem = $this->route('invoice_item');
        if (!$invoiceItem instanceof InvoiceItem && is_numeric($invoiceItem)) {
            $invoiceItem = InvoiceItem::find($invoiceItem);
        }

        $invoice = $invoiceItem instanceof InvoiceItem ? $invoiceItem->invoice : null;

        return $this->authorizeInvoice($invoice);
    }
}
