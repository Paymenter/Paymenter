<?php

namespace Paymenter\Extensions\Gateways\Mollie;

use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Mollie extends Gateway
{
    public function boot()
    {
        require __DIR__ . '/routes.php';
        // Register webhook route
    }

    private function request($url, $method = 'get', $data = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('api_key'),
            'Content-Type' => 'application/json',
        ])->$method('https://api.mollie.com' . $url, $data);

        if (!$response->successful()) {
            throw new \Exception('Mollie API error: ' . $response->json()['detail']);
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
                'label' => 'API Key',
                'type' => 'text',
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
        $response = $this->request('/v2/payments', 'post', [
            'amount' => [
                'currency' => $invoice->currency_code,
                'value' => number_format($total, 2, '.', ''),
            ],
            'description' => 'Invoice #' . $invoice->id,
            'redirectUrl' => route('invoices.show', $invoice) . '?checkPayment=true',
            'webhookUrl' => route('extensions.gateways.mollie.webhook', $invoice),
            'metadata' => [
                'invoice_id' => $invoice->id,
            ],
        ]);

        return $response['_links']['checkout']['href'];
    }

    public function webhook(Request $request)
    {
        $payment = $this->request('/v2/payments/' . $request->input('id'));

        if ($payment['status'] == 'paid') {
            ExtensionHelper::addPayment($payment['metadata']['invoice_id'], 'Mollie', $payment['amount']['value'], transactionId: $payment['id']);
        }
    }
}
