<?php
namespace App\Http\Controllers\API\Clients;

use App\Classes\API;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoices;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Get all invoices of current user
     */
    public function getInvoices(Request $request) {
        $user = $request->user();
        $invoices = $user->invoices()->paginate(25);

        return response()->json([
            'invoices' => API::repaginate($invoices)
        ], 200);
    }

    /**
     * Get invoice by ID
     */
    public function getInvoice(Request $request, int $invoiceId) {
        $user = $request->user();
        $invoice = Invoices::where('id', $invoiceId)->where('user_id', $user->id)->firstOrFail();
        $order = Orders::findOrFail($invoice->order_id);

        $products = [];
        foreach ($order->products()->get() as $product) {
            $item = Products::where('id', $product->product_id)->first();
            $item->quantity = $product['quantity'];
            $products[] = $item;
        }

        return response()->json([
            'invoice' => $invoice,
            'products' => $products,
        ], 200);
    }
    
    /**
     * Pay invoice by ID
     */
    public function payInvoice(Request $request, int $invoiceId) {
        $user = $request->user();
        $invoice = Invoices::where('id', $invoiceId)->where('user_id', $user->id)->firstOrFail();
        $order = Orders::findOrFail($invoice->order_id);

        if ($invoice->status != 'unpaid') {
            return response()->json([
                'error' => 'Invoice is already paid'
            ], 400);
        }

        $products = [];
        $total = $invoice->total;
        foreach ($order->products()->get() as $product) {
            $item = json_decode(Products::where('id', $product->product_id)->first());
            $item->quantity = $product['quantity'];
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
                'error' => 'Payment method not found'
            ], 404);
        } 

        return response()->json([
            'error' => 'Payment method not found'
        ], 404);
    }
}