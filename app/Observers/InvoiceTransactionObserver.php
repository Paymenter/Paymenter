<?php

namespace App\Observers;

use App\Events\InvoiceTransaction as InvoiceTransactionEvent;
use App\Models\InvoiceTransaction;

class InvoiceTransactionObserver
{
    /**
     * Handle the InvoiceTransaction "creating" event.
     */
    public function creating(InvoiceTransaction $invoice): void
    {
        event(new InvoiceTransactionEvent\Creating($invoice));
    }

    /**
     * Handle the InvoiceTransaction "created" event.
     */
    public function created(InvoiceTransaction $invoice): void
    {
        event(new InvoiceTransactionEvent\Created($invoice));
    }

    /**
     * Handle the InvoiceTransaction "updating" event.
     */
    public function updating(InvoiceTransaction $invoice): void
    {
        event(new InvoiceTransactionEvent\Updating($invoice));
    }

    /**
     * Handle the InvoiceTransaction "updated" event.
     */
    public function updated(InvoiceTransaction $invoice): void
    {
        event(new InvoiceTransactionEvent\Updated($invoice));
    }

    /**
     * Handle the InvoiceTransaction "deleted" event.
     */
    public function deleted(InvoiceTransaction $invoice): void
    {
        event(new InvoiceTransactionEvent\Deleted($invoice));
    }
}
