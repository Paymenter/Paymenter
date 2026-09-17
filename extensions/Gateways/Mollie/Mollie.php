<?php

namespace Paymenter\Extensions\Gateways\Mollie;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

#[ExtensionMeta(
    name: 'Mollie Gateway',
    description: 'Accept payments via Mollie.',
    version: 'builtin',
    author: 'Paymenter',
    url: 'https://paymenter.org/docs/extensions/mollie',
    icon: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODciIGhlaWdodD0iNTgiIHZpZXdib3g9IjAgMCA4NyA1OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBjbGFzcz0iZ3JvdXAtaG92ZXIvYXBwOnNjYWxlLTEyMCBmbGV4IGl0ZW1zLWNlbnRlciBqdXN0aWZ5LWNlbnRlciBzaXplLTE4IHJvdW5kZWQteGwgcC00IGJnLWJsYWNrIHJpbmctNiByaW5nLXdoaXRlLzEwIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTYzLjkyMiAwLjg1MjA5NkM2My4xOTkgMC43NjE3MTkgNjIuNDc2IDAuNzYxNzE5IDYxLjc1MjkgMC43NjE3MTlDNTQuNzkzOSAwLjc2MTcxOSA0OC4xOTY1IDMuNTYzMzkgNDMuNDk2OSA4LjYyNDQ4QzM4Ljc5NzMgMy42NTM3NyAzMi4xOTk4IDAuNzYxNzE5IDI1LjMzMTIgMC43NjE3MTlDMTEuNTAzNiAwLjc2MTcxOSAwLjI5Njg3NSAxMS45Njg0IDAuMjk2ODc1IDI1LjYxNTNWNTYuOTc1OUgxMy43NjNWMjYuMDY3MkMxMy43NjMgMjAuMzczNCAxOC40NjI2IDE1LjEzMTYgMjMuOTc1NSAxNC41ODkzQzI0LjMzNyAxNC41ODkzIDI0Ljc4ODkgMTQuNDk5IDI1LjE1MDQgMTQuNDk5QzMxLjM4NjQgMTQuNDk5IDM2LjQ0NzUgMTkuNTYgMzYuNDQ3NSAyNS43OTZWNTcuMDY2M0g1MC4xODQ3VjI1Ljk3NjhDNTAuMTg0NyAyMC4yODMxIDU0Ljg4NDMgMTUuMDQxMiA2MC4zOTczIDE0LjQ5OUM2MC43NTg4IDE0LjQ5OSA2MS4yMTA3IDE0LjQwODYgNjEuNTcyMiAxNC40MDg2QzY3LjgwODIgMTQuNDA4NiA3Mi44NjkzIDE5LjQ2OTcgNzIuOTU5NiAyNS42MTUzVjU2Ljk3NTlIODYuNjk2OVYyNi4wNjcyQzg2LjY5NjkgMTkuNzQwOCA4NC4zNDcxIDEzLjc3NTkgODAuMTg5OCA5LjA3NjM2Qzc1Ljk0MjEgNC4yODY0MSA3MC4xNTggMS4zOTQzNiA2My45MjIgMC44NTIwOTZaIiBmaWxsPSJ3aGl0ZSI+PC9wYXRoPjwvc3ZnPg==',
)]
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
            throw new Exception('Mollie API error: ' . $response->json()['detail']);
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
                'encrypted' => true,
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
            'cancelUrl' => route('invoices.show', $invoice),
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
