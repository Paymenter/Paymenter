<?php

namespace App\Classes;

use App\Models\Invoice;
use App\Helpers\SevDeskHelper;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

class PDF
{
    public static function generateInvoice(Invoice $invoice)
    {
        if (config('settings.invoice_management') === 'sevdesk') {
            $sevDeskHelper = new SevDeskHelper();
            $sevDeskInvoice = $sevDeskHelper->getInvoice($invoice->sevdesk_id);
            return response()->streamDownload(function () use ($sevDeskInvoice) {
                echo $sevDeskInvoice['pdf'];
            }, 'invoice-' . $invoice->id . '.pdf');
        } else {
            $pdf = DomPDF::loadView('pdf.invoice', ['invoice' => $invoice]);

            // Create path if it doesn't exist
            if (!is_dir(storage_path('app/invoices'))) {
                mkdir(storage_path('app/invoices'));
            }
            // Save the PDF to the storage
            $pdf->save(storage_path('app/invoices/' . $invoice->id . '.pdf'));

            return $pdf;
        }
    }
}
