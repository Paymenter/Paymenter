<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::where('user_id', $request->user()->id)->where('credits', null)->with(['items.product.order.coupon', 'items.product.product'])->get()->sort(function ($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });

        return view('clients.invoice.index', compact('invoices'));
    }

    public function show(Request $request, Invoice $invoice)
    {
        if ($invoice->user_id != auth()->user()->id) {
            return redirect()->route('clients.invoice.index');
        }
        if ($invoice->credits) {
            return redirect()->route('clients.credits');
        }

        $invoiceItems = $invoice->getItemsWithProducts();
        $products = $invoiceItems->products;
        $total = $invoiceItems->total;
        $tax = $invoiceItems->tax;
        $currency_sign = config('settings::currency_sign');

        $gateways = ExtensionHelper::getAvailableGateways($total, $products);

        return view('clients.invoice.show', compact('invoice', 'products', 'currency_sign', 'total', 'tax', 'gateways'));
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
            if ($payment_method == 'credits') {
                $user = User::where('id', auth()->user()->id)->first();
                if ($user->credits < $total) {
                    return redirect()->route('clients.invoice.show', $invoice->id)->with('error', 'You do not have enough credits');
                }
                $user->credits = $user->credits - $total;
                $user->save();
                ExtensionHelper::paymentDone($invoice->id);
                return redirect()->route('clients.invoice.show', $invoice->id)->with('success', 'Payment done');
            }
            if ($invoiceItems->tax->amount > 0 && config('settings::tax_type') == 'exclusive') {
                foreach ($products as $product) {
                    $product->price = $product->price + ($product->price * $invoiceItems->tax->rate / 100);
                }
            }
            $payment_method = ExtensionHelper::getPaymentMethod($payment_method, $total, $products, $invoice->id);
            if ($payment_method) {
                return redirect($payment_method);
            }
        }

        return redirect()->back()->with('error', 'Payment method not found');
    }
}
