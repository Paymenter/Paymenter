<?php

namespace App\Listeners;

use App\Events\Invoice\Creating;
use App\Models\Invoice;

class InvoiceCreatingListener
{
    /**
     * Handle the event.
     */
    public function handle(Creating $event): void
    {
        // Get the latest invoice
        $latestInvoice = Invoice::latest()->first();

        // Get the next invoice number
        $number = $latestInvoice ? $latestInvoice->id + 1 : 1;

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
    }
}
