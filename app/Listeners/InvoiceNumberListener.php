<?php

namespace App\Listeners;

use App\Events\AdjustmentNote\Creating as AdjustmentNoteCreating;
use App\Events\CreditNote\Creating as CreditNoteCreating;
use App\Events\Invoice\Creating;
use App\Events\Invoice\Updating;
use App\Models\Model;
use App\Models\Setting;
use App\Models\Invoice;

class InvoiceNumberListener
{
    /**
     * Handle the event.
     */
    public function handle(Creating|Updating|CreditNoteCreating|AdjustmentNoteCreating $event): void
    {
        if ($event instanceof Updating) {
            $isTransitioningFromDraft = $event->invoice->getOriginal('status') === Invoice::STATUS_DRAFT;
            $isChangingToPendingOrPaid = $event->invoice->isDirty('status') &&
                in_array($event->invoice->status, [Invoice::STATUS_PENDING, Invoice::STATUS_PAID]);

            if (($isTransitioningFromDraft && $isChangingToPendingOrPaid && !$event->invoice->number) ||
                ($event->invoice->isDirty('status') && $event->invoice->status === Invoice::STATUS_PAID && !$event->invoice->number)) {
                $this->setNumber($event->invoice, 'invoice');
            }
        } elseif ($event instanceof Creating && !config('settings.invoice_proforma', false)) {
            $this->setNumber($event->invoice, 'invoice');
        } elseif ($event instanceof CreditNoteCreating) {
            $this->setNumber($event->creditNote, 'credit_note');
        } elseif ($event instanceof AdjustmentNoteCreating) {
            $this->setNumber($event->adjustmentNote, 'adjustment_note');
        }
    }

    private function setNumber(Model $model, string $prefix): void
    {
        $number = config("settings.{$prefix}_number", 1) + 1;

        Setting::updateOrCreate([
            'key' => "{$prefix}_number",
        ], [
            'value' => $number,
        ]);

        $paddedNumber = str_pad($number, config("settings.{$prefix}_number_padding", 1), '0', STR_PAD_LEFT);

        $formattedNumber = config("settings.{$prefix}_number_format", '{number}');
        $formattedNumber = str_replace('{number}', $paddedNumber, $formattedNumber);
        $formattedNumber = str_replace('{year}', now()->format('Y'), $formattedNumber);
        $formattedNumber = str_replace('{month}', now()->format('m'), $formattedNumber);
        $formattedNumber = str_replace('{day}', now()->format('d'), $formattedNumber);

        $model->number = $formattedNumber;
    }
}
