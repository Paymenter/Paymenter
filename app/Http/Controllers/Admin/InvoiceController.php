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
        $products = [];
        $total = 0;
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
                $product->order = $order;
                $products[] = $product;
                $total += $product->price - $product->discount;
            } else {
                $product = $item;
                $product->price = $item->total;
                $product->discount = 0;
                $product->quantity = 1;
                $products[] = $product;
                $total += $product->price - $product->discount;
            }
        }

        return view('admin.invoices.show', compact('invoice', 'products', 'total') );
    }

    public function paid(Invoice $invoice)
    {
        ExtensionHelper::paymentDone($invoice->id);

        return redirect()->route('admin.invoices.show', $invoice);
    }
}