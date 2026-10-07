<?php

namespace App\Listeners;

use App\Events\Invoice\Paid;
use App\Services\Invoice\CreateInvoiceSnapshotService;

class CreateInvoiceSnapshotListener
{
    /**
     * Handle the event.
     */
    public function handle(Paid $event): void
    {
        app(CreateInvoiceSnapshotService::class)->handle($event->invoice);
    }
}
