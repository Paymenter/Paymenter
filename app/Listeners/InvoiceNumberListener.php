<?php

namespace App\Listeners;

use App\Events\Invoice\Creating;
use App\Events\Invoice\Updating;
use App\Models\Invoice;
use App\Models\Setting;

class InvoiceNumberListener
{
    /**
     * Handle the event.
     */
    public function handle(Creating|Updating $event): void
    {
        if ($event instanceof Updating) {
            if ($event->invoice->isDirty('status') && $event->invoice->status == 'paid' && !$event->invoice->number) {
                $this->setInvoiceNumber($event);
            }
        } elseif ($event instanceof Creating && !config('settings.invoice_proforma', false)) {
            $this->setInvoiceNumber($event);
        }
    }

    private function setInvoiceNumber(Creating|Updating $event): void
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
    }
}
