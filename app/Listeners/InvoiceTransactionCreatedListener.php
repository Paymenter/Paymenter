<?php

namespace App\Listeners;

use App\Events\InvoiceTransaction\Created;

class InvoiceTransactionCreatedListener
{
    /**
     * Handle the event.
     */
    public function handle(Created $event): void
    {
        $invoice = $event->invoiceTransaction->invoice;
        if ($invoice->remaining <= 0 && $invoice->status !== 'paid') {
            $invoice->status = 'paid';
            $invoice->save();
        }
    }
}
