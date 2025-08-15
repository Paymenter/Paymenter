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

        // Debug: log setting and credit info
        \Log::info('AutoRenewal: credits_auto_renewal_enabled=' . var_export(config('settings.credits_auto_renewal_enabled'), true));
        \Log::info('AutoRenewal: invoice_id=' . $invoice->id . ', user_id=' . $user?->id . ', credit=' . ($credit?->amount ?? 'none') . ', remaining=' . $invoice->remaining);

        // Skip credit deposit invoices
        if ($invoice->items()->whereRaw('LOWER(description) LIKE ?', ['%credit deposit%'])->exists()) {
            \Log::info('AutoRenewal: Skipped credit deposit invoice');
            return;
        }

        // Only pay if user has enough credits and auto-renewal is enabled
        if (
            config('settings.credits_auto_renewal_enabled') &&
            $credit &&
            $credit->amount >= $invoice->remaining
        ) {
            \Log::info('AutoRenewal: Paying invoice with credits');
            $credit->amount -= $invoice->remaining;
            $credit->save();
            \App\Helpers\ExtensionHelper::addPayment($invoice->id, null, amount: $invoice->remaining);
            $invoice->status = 'paid';
            $invoice->save();
        }
    }
}
