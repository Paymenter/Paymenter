<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ExtensionHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Mail\Invoices\NewInvoice;
use App\Models\{Invoice, InvoiceItem, User};
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('admin.invoices.index');
    }

    public function show(Invoice $invoice)
    {
        $invoiceData = $invoice->getItemsWithProducts();
        $products = $invoiceData->products;
        $total = $invoiceData->total;

        return view('admin.invoices.show', compact('invoice', 'products', 'total') );
    }

    public function paid(Invoice $invoice, Request $request)
    {
        $request->validate([
            'paid_with' => 'required',
            'paid_reference' => 'nullable|string',
        ]);
        
        ExtensionHelper::paymentDone($invoice->id, $request->paid_with, $request->paid_reference);

        return redirect()->route('admin.invoices.show', $invoice);
    }

    public function create()
    {
        $users = User::all();
        return view('admin.invoices.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'item_name' => 'required|array',
            'item_price' => 'required|array',
        ]);
        $invoice = new Invoice();
        $invoice->user_id = $request->user_id;
        $invoice->status = 'pending';
        $invoice->saveQuietly();
        foreach ($request->item_name as $key => $item) {
            $invoiceItem = new InvoiceItem();
            $invoiceItem->invoice_id = $invoice->id;
            $invoiceItem->total = $request->item_price[$key];
            $invoiceItem->description = $item;
            $invoiceItem->save();
        }

        event(new \App\Events\Invoice\InvoiceCreated($invoice));
        NotificationHelper::sendNewInvoiceNotification($invoice, User::find($request->user_id));
        return redirect()->route('admin.invoices.show', $invoice);
    }
}
