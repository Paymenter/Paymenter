<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoice;



class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::all();

        return view('admin.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        return view('admin.invoices.show', compact('invoice'));
    }

    public function paid(Invoice $invoice)
    {
        ExtensionHelper::paymentDone($invoice->id);

        return redirect()->route('admin.invoices.show', $invoice);
    }
}