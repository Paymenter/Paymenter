<?php

use App\Helpers\ExtensionHelper;
use Illuminate\Support\Facades\Http;

function Xendit_pay($total, $products, $orderId)
{
    $url = 'https://api.xendit.co/v2/invoices';
    $apiKey = ExtensionHelper::getConfig('Xendit', 'api_key');
    // Encode to base64
    $apiKey = base64_encode($apiKey . ':');
    $description = 'Products: ';
    foreach ($products as $product) {
        $description .= $product->name . ' x' . $product->quantity . ', ';
    }
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic ' . $apiKey,
    ])->post($url, [
        'amount' => number_format($total, 2, '.', ''),
        'description' => $description,
        'success_redirect_url' => route('clients.invoice.show', $orderId),
        'failure_redirect_url' => route('clients.invoice.show', $orderId),
        'external_id' => (string) $orderId,
    ]);

    return $response->json()['invoice_url'];
}

function Xendit_webhook($request)
{
    if ($request->header('x-callback-token') != ExtensionHelper::getConfig('Xendit', 'callback')) {
        return response()->json(['message' => 'Invalid callback token'], 403);
    }
    $json = $request->getContent();
    $json = json_decode($json, true);
    ExtensionHelper::paymentDone($json['external_id']);
    response()->json(['message' => 'Webhook received'], 200);
}

function Xendit_getConfig()
{
    return [
        [
            'name' => 'api_key',
            'friendlyName' => 'API Key',
            'type' => 'text',
            'required' => true,
        ],
        [
            'name' => 'callback',
            'friendlyName' => 'Callback verification token',
            'type' => 'text',
            'required' => true,
        ],
    ];
}
