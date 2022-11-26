<?php

use Illuminate\Support\Facades\Http;
use App\Helpers\ExtensionHelper;

function PayPal_pay($total, $products, $orderId)
{
    $client_id = ExtensionHelper::getConfig('PayPal', 'client_id');
    $client_secret = ExtensionHelper::getConfig('PayPal', 'client_secret');
    $live = ExtensionHelper::getConfig('PayPal', 'live');
    if ($live) {
        $url = 'https://api-m.paypal.com';
    } else {
        $url = 'https://api-m.sandbox.paypal.com';
    }

    error_log('PayPal: ' . $url);
    $response = Http::withHeaders([
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret)
    ])->asForm()->post($url . '/v1/oauth2/token', [
        'grant_type' => 'client_credentials'
    ]);

    $token = $response->json()['access_token'];
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $token
    ])->post($url . '/v2/checkout/orders', [
        'intent' => 'CAPTURE',
        'purchase_units' => [
            [
                'reference_id' => $orderId,
                'amount' => [
                    'currency_code' => 'eur',
                    'value' => $total,
                ]
            ]
        ],
        'application_context' => [
            'cancel_url' => route('invoice.show', ['id' => $orderId]),
            'return_url' => route('invoice.show', ['id' => $orderId])
        ]
    ]);
    error_log(print_r($response->body(), true));
    // Return the link to the payment
    return $response->json()['links'][1]['href'];
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $token
    ])->post($url . '/v2/checkout/orders/' . $orderId . '/capture', [
        'amount' => [
            'currency_code' => 'eur',
            'value' => $total
        ]
    ]);

    error_log(print_r($response->body(), true));
    return $response->json();
}

function PayPal_webhook($request)
{
    // Verify the webhook signature
    $client_id = ExtensionHelper::getConfig('PayPal', 'client_id');
    $client_secret = ExtensionHelper::getConfig('PayPal', 'client_secret');
    $live = ExtensionHelper::getConfig('PayPal', 'live');
    if ($live) {
        $url = 'https://api-m.paypal.com';
    } else {
        $url = 'https://api-m.sandbox.paypal.com';
    }

    $response = Http::withHeaders([
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret)
    ])->asForm()->post($url . '/v1/oauth2/token', [
        'grant_type' => 'client_credentials'
    ]);
    $token = $response->json()['access_token'];

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $token
    ])->post($url . '/v1/notifications/verify-webhook-signature', [
        'transmission_id' => $request->header('Paypal-Transmission-Id'),
        'transmission_time' => $request->header('Paypal-Transmission-Time'),
        'cert_url' => $request->header('Paypal-Cert-Url'),
        'auth_algo' => $request->header('Paypal-Auth-Algo'),
        'transmission_sig' => $request->header('Paypal-Transmission-Sig'),
        'webhook_id' => '1YD84112DH779790K',
        'webhook_event' => $request->all()
    ]);
    error_log(print_r($response->body(), true));
}

function PayPal_getConfig()
{
    return [
        [
            "name" => "client_id",
            "type" => "text",
            "friendlyName" => "Client ID",
            "required" => true
        ],
        [
            "name" => "client_secret",
            "type" => "text",
            "friendlyName" => "Client Secret",
            "required" => true
        ],
        [
            "name" => "live",
            "type" => "boolean",
            "friendlyName" => "Live mode",
            "required" => false
        ]
    ];
}
