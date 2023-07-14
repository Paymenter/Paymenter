<?php

namespace App\Http\Controllers\API\Clients;

use App\Classes\API;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\API\Controller;
use App\Models\Extension;

class InvoiceController extends Controller
{
    /**
     * Get all invoices of current user.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoices(Request $request)
    {
        $user = $request->user();

        if (!$user->tokenCan('invoice:read')) {
            return $this->error('You do not have permission to read invoices.', 403);
        }

        $invoices = $user->invoices()->paginate(25);
        return response()->json([
            'invoices' => API::repaginate($invoices),
        ], 200);
    }

    /**
     * Get invoice by ID.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoice(Request $request, int $invoiceId)
    {
        $user = $request->user();

        if (!$user->tokenCan('invoice:read')) {
            return response()->json([
                'error' => 'You do not have permission to read invoices.',
            ], 403);
        }

        $invoice = Invoice::where('id', $invoiceId)->where('user_id', $user->id)->firstOrFail();
        $order = Order::findOrFail($invoice->order_id);

        $products = [];
        foreach ($order->products()->get() as $product) {
            $item = Product::where('id', $product->product_id)->first();
            $item->quantity = $product['quantity'];
            $products[] = $item;
        }

        return response()->json([
            'invoice' => new InvoiceResource::make($invoice),
            'products' => $products,
        ], 200);
    }

    /**
     * Pay invoice by ID.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function payInvoice(Request $request, int $invoiceId)
    {
        $user = $request->user();

        if (!$user->tokenCan('invoice:update')) {
            return response()->json([
                'error' => 'You do not have permission to update invoices.',
            ], 403);
        }
        if(!$request->has('payment_method')) {
            return response()->json([
                'error' => 'Payment method is required.',
            ], 400);
        }

        $invoice = Invoice::where('id', $invoiceId)->where('user_id', $user->id)->firstOrFail();

        if ($invoice->status == 'paid') {
            return response()->json([
                'error' => 'Invoice is already paid',
            ], 400);
        }

        $products = [];
        $total = $invoice->total;
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
                $total += ($product->price - $product->discount) * $product->quantity;
            } else {
                $product = $item;
                $product->price = $item->total;
                $product->name = $item->description;
                $product->discount = 0;
                $product->quantity = 1;
                $products[] = $product;
                $total += ($product->price - $product->discount) * $product->quantity;
            }
        }

        if ($request->get('payment_method')) {
            $payment_method = $request->get('payment_method');
            $payment_method = Extension::where('type', 'gateway')->where('name', $payment_method)->first();
            $payment_method = ExtensionHelper::getPaymentMethod($payment_method->id, $total, $products, $invoice->id);

            if ($payment_method) {
                return redirect($payment_method);
            }

            return response()->json([
                'error' => 'Payment method not found',
            ], 404);
        }

        return response()->json([
            'error' => 'Payment method not found',
        ], 404);
    }
}
