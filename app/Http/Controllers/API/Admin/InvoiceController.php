<?php

namespace App\Http\Controllers\API\Admin;

use App\Classes\API;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Controller;

class InvoiceController extends Controller
{
    /**
     * Get all invoices.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoices(Request $request)
    {
        $invoices = Invoice::paginate(25);

        return $this->success('Invoices successfully retrieved.', API::repaginate($invoices));
    }

    /**
     * Get invoice by ID.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoice(Request $request, int $invoiceId)
    {
        $user = $request->user();

        if (!$user->tokenCan('admin:invoice:read')) {
            return response()->json([
                'error' => 'You do not have permission to read invoices.',
            ], 403);
        }

        $invoice = Invoice::where('id', $invoiceId)->firstOrFail();
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
}
