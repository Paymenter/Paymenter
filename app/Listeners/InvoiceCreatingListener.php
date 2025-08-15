<?php

namespace App\Listeners;

use App\Events\Invoice\Creating;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Credit;

class InvoiceCreatingListener
{
    /**
     * Handle the event.
     */
    public function handle(Creating $event): void
    {

        // Get the next invoice number
        $number = config('settings.invoice_number', 1) + 1;
        // Update setting
        Setting::updateOrCreate([
            'key' => 'invoice_number',
        ], [
            'value' => $number,
        ]);
        // Pad the invoice number with leading zeros
        $paddedNumber = str_pad($number, config('settings.invoice_number_padding', 1), '0', STR_PAD_LEFT);

        // Format the invoice number
        $formattedNumber = config('settings.invoice_number_format', '{number}');
        $formattedNumber = str_replace('{number}', $paddedNumber, $formattedNumber);
        $formattedNumber = str_replace('{year}', now()->format('Y'), $formattedNumber);
        $formattedNumber = str_replace('{month}', now()->format('m'), $formattedNumber);
        $formattedNumber = str_replace('{day}', now()->format('d'), $formattedNumber);

        // Set the invoice number
        $event->invoice->number = $formattedNumber;


        // Auto-pay with credits if possible (additive)
        $invoice = $event->invoice;
        $user = $invoice->user;
        $credit = $user?->credits()->where('currency_code', $invoice->currency_code)->first();

        // Skip credit deposit invoices
        if ($invoice->items()->whereRaw('LOWER(description) LIKE ?', ['%credit deposit%'])->exists()) {
            return;
        }

        // Only pay if user has enough credits
        if ($credit && $credit->amount >= $invoice->remaining) {
            $credit->amount -= $invoice->remaining;
            $credit->save();
            \App\Helpers\ExtensionHelper::addPayment($invoice->id, null, amount: $invoice->remaining);
            $invoice->status = 'paid';
        }
    }
}
