<?php

namespace App\Listeners;

use App\Events\Invoice\Created;
use App\Models\Credit;

class InvoiceCreatedListener
{
    /**
     * Handle the event.
     */
    public function handle(Created $event): void
    {
        $invoice = $event->invoice;

        // Only run if invoice is still pending
        if ($invoice->status !== 'pending') {
            return;
        }

        $user = $invoice->user;
        $credit = $user?->credits()->where('currency_code', $invoice->currency_code)->first();

        // Skip credit deposit invoices
        if ($invoice->items()->whereRaw('LOWER(description) LIKE ?', ['%credit deposit%'])->exists()) {
            return;
        }

        // Only pay if user has enough credits and auto-renewal is enabled
        if (
            config('settings.credits_auto_renewal_enabled') &&
            $credit &&
            $credit->amount > 0 &&
            $credit->amount >= $invoice->remaining
        ) {
            $credit->amount -= $invoice->remaining;
            $credit->save();
            \App\Helpers\ExtensionHelper::addPayment($invoice->id, null, amount: $invoice->remaining);
            $invoice->status = 'paid';
            $invoice->save();
        }
    }
}