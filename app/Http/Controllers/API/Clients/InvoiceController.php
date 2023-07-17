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

        if (!$user->tokenCan('invoice:read')) {
            return $this->error('You do not have permission to read invoices.', 403);
        }

        $invoice = Invoice::where('id', $invoiceId)->where('user_id', $user->id)->firstOrFail();

        return $this->success('Invoice successfully retrieved.', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Pay invoice by ID.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function payInvoice(Request $request, int $invoiceId)
    {
        $user = $request->user();

        $request->validate([
            'payment_method' => 'required|exists:extensions,name',
        ]);

        if (!$user->tokenCan('invoice:update')) {
            return $this->error('You do not have permission to update invoices.', 403);
        }

        $invoice = Invoice::where('id', $invoiceId)->where('user_id', $user->id)->firstOrFail();

        if ($invoice->status == 'paid') {
            return response()->json([
                'error' => 'Invoice is already paid',
            ], 400);
        }

        $payment_method = Extension::where('type', 'gateway')->where('name', $request->get('payment_method'))->first();

        if (!$payment_method->enabled) {
            return $this->error('Payment method is not enabled.', 400);
        }

        $productsAndTotal = $invoice->getItemsWithProducts();
        $total = $productsAndTotal->total;
        $products = $productsAndTotal->products;

        $payment_method = ExtensionHelper::getPaymentMethod($payment_method->id, $total, $products, $invoice->id);

        if ($payment_method) {
            return redirect($payment_method);
        }

        return $this->error('Payment method not found.', 404);
    }
}
