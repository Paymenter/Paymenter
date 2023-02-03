<?php

use App\Helpers\ExtensionHelper;
use Illuminate\Support\Facades\Http;

function Mollie_pay($total, $products, $orderId)
{
    $url = 'https://api.mollie.com/v2/payments';
    $client_id = ExtensionHelper::getConfig('Mollie', 'api_key');
    $description = 'Products: ';
    foreach ($products as $product) {
        $description .= $product->name . ' x' . $product->quantity . ', ';
    }
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $client_id,
    ])->post($url, [
        'amount' => [
            'currency' => ExtensionHelper::getCurrency(),
            'value' => number_format($total, 2, '.', ''),
        ],
        'description' => $description,
        'redirectUrl' => route('clients.invoice.show', $orderId),
        'webhookUrl' => url('/extensions/mollie/webhook'),
        'metadata' => [
            'order_id' => $orderId,
        ],
    ]);

    return $response->json()['_links']['checkout']['href'];
}

function Mollie_webhook($request)
{
    $url = 'https://api.mollie.com/v2/payments/' . $request->id;
    $client_id = ExtensionHelper::getConfig('Mollie', 'api_key');
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $client_id,
    ])->get($url);
    if ($response->json()['status'] == 'paid') {
        $orderId = $response->json()['metadata']['order_id'];
        ExtensionHelper::paymentDone($orderId);
    }

    return $response->json();
}

function Mollie_getConfig()
{
    return [
        [
            'name' => 'api_key',
            'friendlyName' => 'API Key',
            'type' => 'text',
            'required' => true,
        ],
    ];
}
