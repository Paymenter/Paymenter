<?php

namespace App\Http\Controllers\API\Clients;

use App\Classes\API;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    /**
     * Get all invoices of current user.
     */
    public function getInvoices(Request $request)
    {
        $user = $request->user();

        if (!$user->tokenCan('invoice:read')) {
            return response()->json([
                'error' => 'You do not have permission to read invoices.',
            ], 403);
        }

        $invoices = $user->invoices()->paginate(25);
        return response()->json([
            'invoices' => API::repaginate($invoices),
        ], 200);
    }

    /**
     * Get invoice by ID.
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
            'invoice' => $invoice,
            'products' => $products,
        ], 200);
    }

    /**
     * Pay invoice by ID.
     */
    public function payInvoice(Request $request, int $invoiceId)
    {
        $user = $request->user();

        if (!$user->tokenCan('invoice:update')) {
            return response()->json([
                'error' => 'You do not have permission to update invoices.',
            ], 403);
        }

        $invoice = Invoice::where('id', $invoiceId)->where('user_id', $user->id)->firstOrFail();
        $order = Order::findOrFail($invoice->order_id);

        if ($invoice->status != 'unpaid') {
            return response()->json([
                'error' => 'Invoice is already paid',
            ], 400);
        }

        $products = [];
        $total = $invoice->total;
        foreach ($order->products()->get() as $product) {
            $item = json_decode(Product::where('id', $product->product_id)->first());
            $item->quantity = $product['quantity'];
            $item->price = $product['price'];
            if (isset($product['config'])) {
                $item->config = $product['config'];
            }
            $products[] = $item;
            $total += ($item->price * $item->quantity);
        }

        if ($request->get('payment_method')) {
            $payment_method = $request->get('payment_method');
            $payment_method = ExtensionHelper::getPaymentMethod($payment_method, $total, $products, $invoice->id);

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
