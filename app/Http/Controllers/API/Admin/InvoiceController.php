<?php

namespace App\Http\Controllers\API\Admin;

use App\Classes\API;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoices(Request $request)
    {
        try {
            $invoices = Invoice::paginate(25);

            return $this->success('Invoices successfully retrieved.', API::repaginate($invoices));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving invoices: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $invoiceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoice(Request $request, int $invoiceId)
    {
        try {
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
                if (!$item) {
                    throw new ModelNotFoundException('Product not found with id: ' . $product->product_id);
                }
                $item->quantity = $product['quantity'];
                $products[] = $item;
            }

            return response()->json([
                'invoice' => $invoice,
                'products' => $products,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving the invoice: ' . $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving the invoice: ' . $e->getMessage(),
            ], 500);
        }
    }
}
