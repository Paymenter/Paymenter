<?php

namespace App\Observers;

use App\Events\Invoice as InvoiceEvent;
use App\Models\Invoice;
use App\Services\Invoice\ProcessPaidInvoiceService;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    /**
     * Handle the Invoice "creating" event.
     */
    public function creating(Invoice $invoice): void
    {
        if ($invoice->status === Invoice::STATUS_DRAFT) {
            Log::info('status draft creating');
            return;
        }

        event(new InvoiceEvent\Creating($invoice));
    }

    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        if ($invoice->status === Invoice::STATUS_DRAFT) {
            Log::info('status draft created');
            return;
        }

        event(new InvoiceEvent\Created($invoice));

        $sendEmail = $invoice->send_create_email;

        dispatch(function () use ($invoice, $sendEmail) {
            event(new InvoiceEvent\Finalized($invoice, $sendEmail));
        })->afterResponse();
    }

    /**
     * Handle the Invoice "updating" event.
     */
    public function updating(Invoice $invoice): void
    {
        event(new InvoiceEvent\Updating($invoice));
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        if($invoice->getOriginal('status') === Invoice::STATUS_DRAFT && $invoice->status == Invoice::STATUS_PENDING) {
            event(new InvoiceEvent\Created($invoice));

            $sendEmail = $invoice->send_create_email;

            dispatch(function () use ($invoice, $sendEmail) {
                event(new InvoiceEvent\Finalized($invoice, $sendEmail));
            })->afterResponse();
        }

        if ($invoice->isDirty('status') && $invoice->status == 'paid') {
            app(ProcessPaidInvoiceService::class)->handle($invoice);
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
