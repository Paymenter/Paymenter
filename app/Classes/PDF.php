<?php

namespace App\Classes;

use App\Models\Invoice;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Mpdf;

class PDF
{
    public static function generateInvoice(Invoice $invoice)
    {
        $pdf = Mpdf::loadView('pdf.invoice', ['invoice' => $invoice]);

        return $pdf;
    }
}
