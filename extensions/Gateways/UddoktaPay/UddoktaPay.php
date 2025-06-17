<?php

namespace Paymenter\Extensions\Gateways\UddoktaPay;

use App\Classes\Extension\Gateway;
use App\Exceptions\DisplayException;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UddoktaPay extends Gateway
{
    public function boot()
    {
        require __DIR__ . '/routes.php';
        // Register webhook route
    }

    private function request($url, $data = [])
    {
        $response = Http::withHeaders([
            'X-API-KEY' => $this->config('api_key'),
            'Content-Type' => 'application/json',
        ])->post(rtrim($this->config('base_url'), '/') . $url, $data);

        if (!$response->successful()) {
            throw new DisplayException('UddoktaPay API error: ' . $response->json()['message']);
        }

        return $response->json();
    }

    /**
     * Get all the configuration for the extension
     *
     * @param  array  $values
     * @return array
     */
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'api_key',
                'label' => 'API KEY',
                'type' => 'text',
                'description' => 'You can find your API KEY under System Settings > API Settings.',
                'required' => true,
            ],
            [
                'name' => 'base_url',
                'label' => 'BASE URL',
                'type' => 'text',
                'description' => 'You can find your BASE URL under System Settings > API Settings.',
                'required' => true,
            ],
        ];
    }

    /**
     * Return a view or a url to redirect to
     *
     * @param  float  $total
     * @return string
     */
    public function pay(Invoice $invoice, $total)
    {
        $product = $this->getProduct($invoice);

        $response = $this->request('/checkout/payment/create', [
            'amount' => number_format($total, 2, '.', ''),
            'currency' => $invoice->currency_code,
            'metadata' => [
                'invoice_id' => $invoice->id,
            ],
            'customer' => [
                'name' => $invoice->user->name,
                'email' => $invoice->user->email,
            ],
            'product' => [
                'name' => $product->name ?? 'None',
                'description' => 'Invoice #' . $invoice->id,
            ],
            'success_url' => route('invoices.show', $invoice) . '?checkPayment=true',
            'cancel_url' => route('invoices.show', $invoice),
            'ipn_url' => route('extensions.gateways.uddoktapay.webhook', $invoice),
        ]);

        return $response['paymentURL'];
    }

    private function getProduct(Invoice $invoice): ?Product
    {
        $product = null;

        foreach ($invoice->items as $item) {
            if ($item->reference_type !== Service::class) {
                continue;
            }

            $product = $item->reference->product;
            break;
        }

        return $product;
    }

    public function webhook(Request $request)
    {
        $payment = $this->request('/checkout/payment/verify', [
            'paymentID' => $request->input('paymentID'),
        ]);

        if ($payment['status'] == 'completed') {
            ExtensionHelper::addPayment($payment['metadata']['invoice_id'], 'UddoktaPay', $payment['amount'], $payment['fee'], $payment['transactionID']);
        }
    }
}
