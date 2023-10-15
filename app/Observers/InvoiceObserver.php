<?php

namespace App\Observers;

use App\Events\Invoice\InvoiceCreated;
use App\Events\Invoice\InvoiceDeleted;
use App\Events\Invoice\InvoicePaid;
use App\Models\Invoice;
use App\Models\User;

class InvoiceObserver
{
    /**
     * Handle the Invoice "created" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function created(Invoice $invoice)
    {
        event(new InvoiceCreated($invoice));
    }

    /**
     * Handle the Invoice "updated" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function updated(Invoice $invoice)
    {
        if ($invoice->isPaid()) {
            $affiliateUser = $invoice->user->affiliateUser;
            if ($affiliateUser) {
                $user = User::find($affiliateUser->affiliate->user_id);
                $user->credits += round($invoice->total() * $affiliateUser->affiliate->commission / 100, 2);
                $user->save();
            }
            event(new InvoicePaid($invoice));
        }
    }

    /**
     * Handle the Invoice "deleted" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function deleted(Invoice $invoice)
    {
        event(new InvoiceDeleted($invoice));
    }

    /**
     * Handle the Invoice "restored" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function restored(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function forceDeleted(Invoice $invoice)
    {
        //
    }
}
