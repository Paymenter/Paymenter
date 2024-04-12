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
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvoiceController extends Controller
{
    /**
     * Get all invoices of current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoices(Request $request)
    {
        try {
            $user = $request->user();

            $invoices = $user->invoices()->where('credits', null)->paginate(25);
            return $this->success('Invoices successfully retrieved.', API::repaginate($invoices));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving invoices: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get invoice by ID.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoice(Request $request, int $invoiceId)
    {
        try {
            $user = $request->user();

            if (!$user->tokenCan('invoice:read')) {
                return response()->json([
                    'error' => 'You do not have permission to read invoices.',
                ], 403);
            }

            $invoice = Invoice::where('id', $invoiceId)->where('user_id', $user->id)->firstOrFail();

            return response()->json([
                'invoice' => $invoice,
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

    /**
     * Pay invoice by ID.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function payInvoice(Request $request, int $invoiceId)
    {
        try {
            $user = $request->user();

            $request->validate([
                'payment_method' => 'required|string',
            ]);

            $invoice = Invoice::where('id', $invoiceId)->where('user_id', $user->id)->firstOrFail();

            if ($invoice->status == 'paid') {
                return response()->json([
                    'error' => 'Invoice already paid.',
                ], 400);
            }

            $productsAndTotal = $invoice->getItemsWithProducts();
            $total = $productsAndTotal->total;
            $products = $productsAndTotal->products;
            $payment_method = $request->get('payment_method');

            if ($payment_method == 'credits') {
                if ($user->credits < $total) {
                    return response()->json([
                        'error' => 'You do not have enough credits.',
                    ], 400);
                }
                $user->credits = $user->credits - $total;
                $user->save();
                $invoice->status = 'paid';
                $invoice->save();
                return response()->json([
                    'invoice' => $invoice,
                ], 200);
            }

            $payment_method = Extension::where('type', 'gateway')->where('name', $payment_method)->first();

            if (!$payment_method || !$payment_method->enabled) {
                return response()->json([
                    'error' => 'Payment method is not enabled.',
                ], 400);
            }

            $payment_method = ExtensionHelper::getPaymentMethod($payment_method->id, $total, $products, $invoice->id);

            if ($payment_method) {
                return redirect($payment_method);
            }

            return response()->json([
                'error' => 'Payment method not found.',
            ], 404);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'An error occurred while paying the invoice: ' . $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while paying the invoice: ' . $e->getMessage(),
            ], 500);
        }
    }
}
