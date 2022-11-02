<?php

use Illuminate\Support\Facades\Http;
use App\Helpers\ExtensionHelper;

function PayPal_pay($total, $products, $orderId)
{
    $client_id = ExtensionHelper::getConfig('PayPal', 'client_id');
    $client_secret = ExtensionHelper::getConfig('PayPal', 'client_secret');
    $live = ExtensionHelper::getConfig('PayPal', 'live');
    if($live) {
        $url = 'https://api.paypal.com';
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
    ])->post($url . '/v2/checkout/orders', [
        'intent' => 'AUTHORIZE',
        'purchase_units' => [
            [
                'reference_id' => $orderId,
                'amount' => [
                    'currency_code' => 'eur',
                    'value' => $total * 100
                ]
            ]
        ],
        'application_context' => [
            'cancel_url' => route('invoice.show', ['id' => $orderId]),
            'return_url' => route('invoice.show', ['id' => $orderId])
        ]
    ]);
    error_log(print_r($response->body(), true));

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

function PayPal_webhook($request){
    // Verify the webhook signature
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://ipnpb.paypal.com/cgi-bin/webscr');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'cmd=_notify-validate&' . $request->getContent());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if($response == 'VERIFIED'){
        error_log(print_r($request->getContent(), true));
    }
}