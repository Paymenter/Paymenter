<?php

namespace Paymenter\Extensions\Gateways\MercadoPago;

use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MercadoPago extends Gateway
{
    /**
     * Initializes the extension and registers required routes.
     */
    public function boot()
    {
        require __DIR__ . '/routes.php';
        // Registers the webhook route
    }

    /**
     * Makes a request to the Mercado Pago API.
     *
     * @param string $url The API endpoint (e.g., '/v1/payments')
     * @param string $method The HTTP method (get, post)
     * @param array $data The data to be sent in the request body
     * @param array $extraHeaders Additional headers (e.g., X-Idempotency-Key)
     * @return array API response in JSON format
     * @throws \Exception If the request fails
     */
    private function request($url, $method = 'get', $data = [], $extraHeaders = [])
    {
        $headers = array_merge([
            'Authorization' => 'Bearer ' . $this->config('access_token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], $extraHeaders);

        $response = Http::withHeaders($headers)->$method('https://api.mercadopago.com' . $url, $data);

        if (!$response->successful()) {
            throw new \Exception('Mercado Pago API error: ' . $response->json()['message'] ?? 'Unknown error');
        }

        return $response->json();
    }

    /**
     * Returns the configuration fields required for the extension.
     *
     * @param array $values Current configuration values (optional)
     * @return array List of configuration fields
     */
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'access_token',
                'label' => 'Access Token',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'expiration_minutes',
                'label' => 'Expiration Time (in minutes)',
                'type' => 'number',
                'required' => true,
            ],
        ];
    }

    /**
     * Initiates the payment process and returns the payment URL.
     *
     * @param Invoice $invoice The invoice associated with the payment
     * @param float $total The total amount to be paid
     * @return string URL for the payment (ticket_url)
     */
    public function pay(Invoice $invoice, $total)
    {
        // Get expiration time from config
        $expirationMinutes = $this->config('expiration_minutes');

        // Calculate expiration date (current time + expiration minutes) in ISO 8601 format format: yyyy-MM-dd'T'HH:mm:ssz
        $expirationDate = now()->addMinutes($expirationMinutes)->format('Y-m-d\TH:i:s.000Z');

        $response = $this->request('/v1/payments', 'post', [
            'transaction_amount' => floatval(number_format($total, 2, '.', '')),
            'description' => 'Invoice #' . $invoice->id,
            'payment_method_id' => 'pix',
            'notification_url' => route('extensions.gateways.mercadopago.webhook', $invoice),
            'external_reference' => (string) $invoice->id,
            'date_of_expiration' => $expirationDate, // Sets the expiration date for the payment
        ], [
            'X-Idempotency-Key' => uniqid(), // Ensures the request is not duplicated
        ]);

        return $response['point_of_interaction']['transaction_data']['ticket_url'];
    }

    /**
     * Processes webhook notifications sent by Mercado Pago.
     *
     * @param Request $request The incoming webhook request
     * @return void
     */
    public function webhook(Request $request)
    {
        $paymentId = $request->input('data.id');
        $payment = $this->request('/v1/payments/' . $paymentId);

        if ($payment['status'] === 'approved') {
            ExtensionHelper::addPayment($payment['external_reference'], 'MercadoPago', $payment['transaction_amount'], transactionId: $payment['id']);
        }
    }
}
