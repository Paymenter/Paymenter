<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\{Invoice, Order, Product};

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::where('user_id', auth()->user()->id)->get();

        return view('clients.invoice.index', compact('invoices'));
    }

    public function show(Request $request, Invoice $invoice)
    {
        if ($invoice->user_id != auth()->user()->id) {
            return redirect()->route('clients.invoice.index');
        }
        $products = [];
        foreach ($invoice->items()->get() as $item) {
            if ($item->product_id) {
                $product = $item->product()->get()->first();
                $order = $product->order()->get()->first();
                $coupon = $order->coupon()->get()->first();
                if ($coupon) {
                    if ($coupon->time == 'onetime') {
                        $invoices = $order->invoices()->get();
                        if ($invoices->count() == 1) {
                            $coupon = $order->coupon()->get()->first();
                        } else {
                            $coupon = null;
                        }
                    }
                }

                if ($coupon) {
                    if (!in_array($product->id, $coupon->products) && !empty($coupon->products)) {
                        $product->discount = 0;
                    } else {
                        if ($coupon->type == 'percent') {
                            $product->discount = $product->price * $coupon->value / 100;
                        } else {
                            $product->discount = $coupon->value;
                        }
                    }
                } else {
                    $product->discount = 0;
                }
                $product->description = $item->description;
                $product->price = $item->total;
                $products[] = $product;
            }
        }
        $currency_sign = config('settings::currency_sign');

        return view('clients.invoice.show', compact('invoice', 'order', 'products', 'currency_sign'));
    }

    public function pay(Request $request, Invoice $invoice)
    {
        if ($invoice->user_id != auth()->user()->id) {
            return redirect()->route('clients.invoice.index');
        }
        if ($invoice->status == 'paid') {
            return redirect()->route('clients.invoice.show', $invoice)->with('error', 'Invoice already paid');
        }
        $order = Order::findOrFail($invoice->order_id);
        $total = $invoice->total;
        $products = [];
        $coupon = $order->coupon()->get()->first();

        // Check if the coupon is lifetime or not
        if ($coupon) {
            if ($coupon->time == 'onetime') {
                $invoices = $order->invoices()->get();
                if ($invoices->count() == 1) {
                    $coupon = $order->coupon()->get()->first();
                } else {
                    $coupon = null;
                }
            } else {
                $coupon = $order->coupon()->get()->first();
            }
        }

        foreach ($invoice->items()->get() as $item) {
            if ($item->product_id) {
                $product = $item->product()->get()->first();
                $order = $product->order()->get()->first();
                $coupon = $order->coupon()->get()->first();
                if ($coupon) {
                    if ($coupon->time == 'onetime') {
                        $invoices = $order->invoices()->get();
                        if ($invoices->count() == 1) {
                            $coupon = $order->coupon()->get()->first();
                        } else {
                            $coupon = null;
                        }
                    }
                }

                if ($coupon) {
                    if (!in_array($product->id, $coupon->products) && !empty($coupon->products)) {
                        $product->discount = 0;
                    } else {
                        if ($coupon->type == 'percent') {
                            $product->discount = $product->price * $coupon->value / 100;
                        } else {
                            $product->discount = $coupon->value;
                        }
                    }
                } else {
                    $product->discount = 0;
                }
                $product->name = $item->description;
                $product->price = $item->total;
                $products[] = $product;
                $total += $product->price * $product->quantity;
            } 
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
