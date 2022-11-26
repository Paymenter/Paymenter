<?php
namespace App\Http\Controllers;

use App\Helpers\ExtensionHelper;
use App\Models\Orders;
use App\Models\Products;
use App\Models\User;
use App\Models\Invoices;
use App\Models\Settings;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoices::where('user_id', auth()->user()->id)->get();
        return view('invoice.index', compact('invoices'));
    }

    public function show(Request $request, Invoices $id)
    {
        $order = Orders::findOrFail($id->order_id);
        $invoice = $id;
        ExtensionHelper::setOrderProductConfig('external_id', 1, 7);
        ExtensionHelper::terminateServer($order);

        if($invoice->user_id != auth()->user()->id) {
            return redirect()->route('invoice.index');
        }
        $products = [];
        foreach($order->products()->get() as $product) {
            $test = Products::where('id', $product->product_id)->first();
            $test->quantity = $product['quantity'];
            $products[] = $test;
        }
        $currency_sign = config('settings::currency_sign');
        return view('invoice.show', compact('invoice', 'order', 'products', 'currency_sign'));
    }

    public function pay(Request $request, Invoices $id)
    {
        $invoice = $id;
        if($invoice->user_id != auth()->user()->id) {
            return redirect()->route('invoice.index');
        }
        $order = Orders::findOrFail($invoice->order_id);
        $total = $invoice->total;
        $products = [];
        foreach($order->products()->get() as $product) {
            $test = json_decode(Products::where('id', $product->product_id)->first());
            $test->quantity = $product['quantity'];
            if(isset($product['config'])) {
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