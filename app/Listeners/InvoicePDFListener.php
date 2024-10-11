<?php

namespace App\Listeners;

use App\Classes\PDF;
use App\Events\Invoice\Created;
use App\Events\Invoice\Updated;
use App\Events\InvoiceItem\Created as InvoiceItemCreated;
use App\Events\InvoiceItem\Updated as InvoiceItemUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoicePDFListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(Created|Updated|InvoiceItemCreated|InvoiceItemUpdated $event): void
    {
        // Generate PDF
        // Is a item updated or created?
        if ($event instanceof InvoiceItemCreated || $event instanceof InvoiceItemUpdated) {
            $invoice = $event->invoiceItem->invoice;
        } else {
            $invoice = $event->invoice;
        }
        PDF::generateInvoice($invoice);
    }
}
