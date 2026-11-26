<?php

namespace App\Listeners;

use App\Events\AdjustmentNote\Created as AdjustmentNoteCreated;
use App\Events\AdjustmentNote\Updated as AdjustmentNoteUpdated;
use App\Events\InvoiceTransaction\Created as InvoiceTransactionCreated;
use App\Events\InvoiceTransaction\Updated as InvoiceTransactionUpdated;
use App\Models\Invoice;

class InvoiceSettlementListener
{
    /**
     * Handle invoice transaction and adjustment note changes.
     */
    public function handle(AdjustmentNoteCreated|AdjustmentNoteUpdated|InvoiceTransactionCreated|InvoiceTransactionUpdated $event): void
    {
        $invoice = match (true) {
            $event instanceof AdjustmentNoteCreated,
            $event instanceof AdjustmentNoteUpdated => $event->adjustmentNote->invoice,
            default => $event->invoiceTransaction->invoice,
        };

        if ($invoice->status !== Invoice::STATUS_CANCELLED && $invoice->remaining <= 0 && $invoice->status !== Invoice::STATUS_PAID) {
            $invoice->status = Invoice::STATUS_PAID;
            $invoice->save();
        }
    }
}
