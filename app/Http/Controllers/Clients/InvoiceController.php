<?php

namespace App\Http\Controllers\Clients;

use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\{Products, Invoices, Orders};
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoices::where('user_id', auth()->user()->id)->get();
        return view('clients.invoice.index', compact('invoices'));
    }

    public function show(Request $request, Invoices $id)
    {
        $order = Orders::findOrFail($id->order_id);
        $invoice = $id;

        if ($invoice->user_id != auth()->user()->id) {
            return redirect()->route('clients.invoice.index');
        }
        $products = [];
        foreach ($order->products()->get() as $product) {
            $test = Products::where('id', $product->product_id)->first();
            $test->quantity = $product['quantity'];
            $products[] = $test;
        }
        $currency_sign = config('settings::currency_sign');
        return view('clients.invoice.show', compact('invoice', 'order', 'products', 'currency_sign'));
    }

    public function pay(Request $request, Invoices $id)
    {
        $invoice = $id;
        if ($invoice->user_id != auth()->user()->id) {
            return redirect()->route('clients.invoice.index');
        }
        $order = Orders::findOrFail($invoice->order_id);
        $total = $invoice->total;
        $products = [];
        foreach ($order->products()->get() as $product) {
            $test = json_decode(Products::where('id', $product->product_id)->first());
            $test->quantity = $product['quantity'];
            if (isset($product['config'])) {
                $test->config = $product['config'];
            }
            $products[] = $test;
            $total += $test->price * $test->quantity;
        }

        if ($request->get('payment_method')) {
            $payment_method = $request->get('payment_method');
            $payment_method = ExtensionHelper::getPaymentMethod($payment_method, $total, $products, $invoice->id);
            if ($payment_method) {
                return redirect($payment_method);
            } else {
                return redirect()->back()->with('error', 'Payment method not found');
            }
        } else {
            return redirect()->back()->with('error', 'Payment method not found');
        }
    }
}
