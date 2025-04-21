<?php

namespace App\Observers;

use App\Events\Invoice as InvoiceEvent;
use App\Models\Invoice;

class InvoiceObserver
{
    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        $sendEmail = $invoice->send_create_email;

        dispatch(function () use ($invoice, $sendEmail) {
            $invoice->load('items');
            event(new InvoiceEvent\Created($invoice, $sendEmail));
        })->afterResponse();
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        if ($invoice->isDirty('status') && $invoice->status == 'paid') {
            event(new InvoiceEvent\Paid($invoice));
        }
        event(new InvoiceEvent\Updated($invoice));
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        event(new InvoiceEvent\Deleted($invoice));
    }
}
