<?php

namespace App\Services\Invoice;

use App\Classes\Settings;
use App\Models\Invoice;

class CreateInvoiceSnapshotService
{
    public function handle(Invoice $invoice): void
    {
        if (!config('settings.invoice_snapshot', true) || $invoice->snapshot) {
            return;
        }

        $snapshotData = [
            'name' => $invoice->user?->name,
            'properties' => $invoice->user_properties,
            'bill_to' => config('settings.bill_to_text', config('settings.company_name')),
        ];

        if ($tax = Settings::tax($invoice->user)) {
            $snapshotData['tax_name'] = $tax->name;
            $snapshotData['tax_rate'] = $tax->rate;
            $snapshotData['tax_country'] = $tax->country;
        }

        $invoice->snapshot()->create($snapshotData);
    }
}
