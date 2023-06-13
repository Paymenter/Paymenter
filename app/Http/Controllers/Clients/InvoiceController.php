<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::where('user_id', $request->user()->id)->get()->sort(function ($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });

        return view('clients.invoice.index', compact('invoices'));
    }

    public function show(Request $request, Invoice $invoice)
    {
        if ($invoice->user_id != auth()->user()->id) {
            return redirect()->route('clients.invoice.index');
        }

        $invoiceItems = $invoice->getItemsWithProducts();
        $products = $invoiceItems->products;
        $total = $invoiceItems->total;
        $currency_sign = config('settings::currency_sign');

        return view('clients.invoice.show', compact('invoice', 'products', 'currency_sign', 'total'));
    }

    public function pay(Request $request, Invoice $invoice)
    {
        if ($invoice->user_id != auth()->user()->id) {
            return redirect()->route('clients.invoice.index');
        }

        if ($invoice->status == 'paid') {
            return redirect()->route('clients.invoice.show', $invoice)->with('error', 'Invoice already paid');
        }

        $invoiceItems = $invoice->getItemsWithProducts();
        $products = $invoiceItems->products;
        $total = $invoiceItems->total;

        if ($request->get('payment_method')) {
            $payment_method = $request->get('payment_method');
            $payment_method = ExtensionHelper::getPaymentMethod($payment_method, $total, $products, $invoice->id);
            if ($payment_method) {
                return redirect($payment_method);
            }
        }
        
        return redirect()->back()->with('error', 'Payment method not found');
    }
}
