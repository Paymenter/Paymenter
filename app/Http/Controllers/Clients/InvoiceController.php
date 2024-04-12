<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $invoices = $user->invoices()->where('credits', null)->with(['items.product.order.coupon', 'items.product.product'])->get()->sort(function ($a, $b) {
                return strtotime($b->created_at) - strtotime($a->created_at);
            });

            return view('clients.invoice.index', compact('invoices'));
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while retrieving invoices: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Invoice $invoice)
    {
        try {
            $user = $request->user();

            if ($invoice->user_id != $user->id) {
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
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while retrieving the invoice: ' . $e->getMessage());
        }
    }

    /**
     * Pay the specified invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pay(Request $request, Invoice $invoice)
    {
        try {
            $user = $request->user();

            if ($invoice->user_id != $user->id) {
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
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while paying the invoice: ' . $e->getMessage());
        }
    }
}
