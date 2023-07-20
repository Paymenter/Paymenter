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

        $invoices = $user->invoices()->where('credits', null)->paginate(25);
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
            'payment_method' => 'required|string',
        ]);

        $invoice = Invoice::where('id', $invoiceId)->where('user_id', $user->id)->firstOrFail();

        if ($invoice->status == 'paid') {
            return $this->error('Invoice already paid.', 400);
        }

        $productsAndTotal = $invoice->getItemsWithProducts();
        $total = $productsAndTotal->total;
        $products = $productsAndTotal->products;
        $payment_method = $request->get('payment_method');

        if ($payment_method == 'credits') {
            if ($user->credits < $total) {
                return $this->error('You do not have enough credits.', 400);
            }
            $user->credits = $user->credits - $total;
            $user->save();
            $invoice->status = 'paid';
            $invoice->save();
            return $this->success('Invoice successfully paid.', [
                'invoice' => $invoice,
            ]);
        }

        $payment_method = Extension::where('type', 'gateway')->where('name', $payment_method)->first();

        if (!$payment_method || !$payment_method->enabled) {
            return $this->error('Payment method is not enabled.', 400);
        }

        $payment_method = ExtensionHelper::getPaymentMethod($payment_method->id, $total, $products, $invoice->id);

        if ($payment_method) {
            return redirect($payment_method);
        }

        return $this->error('Payment method not found.', 404);
    }
}
