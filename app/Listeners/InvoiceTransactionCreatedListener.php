<?php

namespace App\Listeners;

use App\Events\InvoiceTransaction\Created;
use App\Events\InvoiceTransaction\Updated;

class InvoiceTransactionCreatedListener
{
    /**
     * Handle the event.
     */
    public function handle(Created|Updated $event): void
    {
        $invoice = $event->invoiceTransaction->invoice;
        if ($invoice->remaining <= 0 && $invoice->status !== 'paid') {
            $invoice->status = 'paid';
            $invoice->save();
        }
    }
}
