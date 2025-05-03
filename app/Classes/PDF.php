<?php

namespace App\Classes;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

class PDF
{
    public static function generateInvoice(Invoice $invoice)
    {
        $pdf = DomPDF::loadView('pdf.invoice', ['invoice' => $invoice]);

        return $pdf;
    }
}
