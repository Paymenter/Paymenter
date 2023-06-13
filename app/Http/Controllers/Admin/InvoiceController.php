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
        $invoice->save();
        foreach ($request->item_name as $key => $item) {
            $invoiceItem = new InvoiceItem();
            $invoiceItem->invoice_id = $invoice->id;
            $invoiceItem->total = $request->item_price[$key];
            $invoiceItem->description = $item;
            $invoiceItem->save();
        }
        NotificationHelper::sendNewInvoiceNotification($invoice, User::find($request->user_id));
        return redirect()->route('admin.invoices.show', $invoice);
    }
}