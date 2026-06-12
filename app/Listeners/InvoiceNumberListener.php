<?php

namespace App\Listeners;

use App\Events\AdjustmentNote\Creating as AdjustmentNoteCreating;
use App\Events\Invoice\Creating;
use App\Events\Invoice\Updating;
use App\Models\AdjustmentNote;
use App\Models\Setting;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceNumberListener
{
    /**
     * Handle the event.
     */
    public function handle(Creating|Updating|AdjustmentNoteCreating $event): void
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
        } elseif ($event instanceof AdjustmentNoteCreating) {
            $this->setNumber($event->adjustmentNote, 'adjustment_note');
        }
    }

    private function setNumber(Invoice|AdjustmentNote $model, string $prefix): void
    {
        $formattedNumber = DB::transaction(function () use ($prefix) {
            $settingKey = $prefix . '_number';
            $setting = Setting::where('key', $settingKey)->lockForUpdate()->first();
            $number = (int) ($setting?->value ?? 0);
            $number++;

            Setting::updateOrCreate([
                'key' => $settingKey,
            ], [
                'value' => $number,
            ]);

            return $this->formatInvoiceNumber($number, $prefix);
        });

        $model->number = $formattedNumber;
    }

    private function formatInvoiceNumber(int $number, string $prefix): string
    {
        // Pad the invoice number with leading zeros
        $paddedNumber = str_pad($number, config("settings.{$prefix}_number_padding", 1), '0', STR_PAD_LEFT);

        $formattedNumber = config("settings.{$prefix}_number_format", '{number}');
        $formattedNumber = str_replace('{number}', $paddedNumber, $formattedNumber);
        $formattedNumber = str_replace('{year}', now()->format('Y'), $formattedNumber);
        $formattedNumber = str_replace('{month}', now()->format('m'), $formattedNumber);
        $formattedNumber = str_replace('{day}', now()->format('d'), $formattedNumber);

        return $formattedNumber;
    }
}
