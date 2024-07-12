<?php

namespace App\Extensions\Gateways\PayPal;

use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPal extends Gateway
{
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'client_id',
                'label' => 'Client ID',
                'type' => 'text',
                'description' => 'Find your API keys at https://developer.paypal.com/developer/applications',
                'required' => true,
            ],
            [
                'name' => 'client_secret',
                'label' => 'Client Secret',
                'type' => 'text',
                'description' => 'Find your API keys at https://developer.paypal.com/developer/applications',
                'required' => true,
            ],
            [
                'name' => 'webhook_id',
                'label' => 'Webhook ID',
                'type' => 'text',
                'description' => 'Find your webhook ID at https://developer.paypal.com/developer/webhooks',
                'required' => true,
            ],
            [
                'name' => 'test_mode',
                'label' => 'Test Mode',
                'type' => 'checkbox',
                'description' => 'Enable test mode',
                'required' => false,
            ],
        ];
    }

    private function generateAccessToken()
    {
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

        return Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->config('client_id') . ':' . $this->config('client_secret')),
        ])->asForm()->post($url . '/v1/oauth2/token', [
            'grant_type' => 'client_credentials',
        ])->object()->access_token;
    }

    public function pay($invoice, $total)
    {
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        $paymentIntent = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->generateAccessToken(),
        ])->post($url . '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $invoice->id,
                    'amount' => [
                        'currency_code' => $invoice->currency_code,
                        'value' => $total,
                    ],
                ],
            ],
            'application_context' => [
                'return_url' => route('invoices.show', ['invoice' => $invoice->id]),
                'cancel_url' => route('invoices.show', ['invoice' => $invoice->id]),
            ],
        ])->object();

        Log::info('PayPal payment intent', (array) $paymentIntent);

        return view('extensions::gateways.paypal.pay', ['invoice' => $invoice, 'total' => $total, 'paymentIntent' => $paymentIntent, 'clientId' => $this->config('client_id')]);
    }

    public function capture(Request $request)
    {
        if (!$request->has('orderID')) {
            abort(400);
        }
        $orderID = $request->input('orderID');
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->generateAccessToken(),
        ])->post($url . '/v2/checkout/orders/' . $orderID . '/capture', [
            'intent' => 'CAPTURE',
        ])->object();

        ExtensionHelper::addPayment($response->purchase_units[0]->reference_id, 'PayPal', $response->purchase_units[0]->payments->captures[0]->amount->value, $response->purchase_units[0]->payments->captures[0]->seller_receivable_breakdown->paypal_fee->value, $response->id);

        return $response;
    }
}
