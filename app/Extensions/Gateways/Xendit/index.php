<?php

use Illuminate\Support\Facades\Http;
use App\Helpers\ExtensionHelper;


function Xendit_pay($total, $products, $orderId)
{
    Xendit_setWebhookURL();
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
        'Authorization' => 'Basic ' . $apiKey
    ])->post($url, [
        'amount' => number_format($total, 2, '.', ''),
        'description' => $description,
        'success_redirect_url' => route('clients.invoice.show', ['id' => $orderId]),
        'failure_redirect_url' => route('clients.invoice.show', ['id' => $orderId]),
        'external_id' => (string) $orderId
    ]);
    return $response->json()['invoice_url'];
};

function Xendit_webhook($request)
{
    error_log(print_r($request->json(), true));
    error_log(print_r($request->header('x-callback-token'), true));
    error_log(print_r(ExtensionHelper::getConfig('Xendit', 'callback'), true));
    // Check if header x-callback-token is equal to the callback token in the config
    if($request->header('x-callback-token') != ExtensionHelper::getConfig('Xendit', 'callback')){
        return response()->json(['message' => 'Invalid callback token'], 403);
    }
    error_log($request->json()->external_id);
    ExtensionHelper::paymentDone($request->json()['external_id']);
};

function Xendit_getConfig()
{
    return [
        [
            "name" => "api_key",
            "friendlyName" => "API Key",
            "type" => "text",
            "required" => true
        ],
        [
            "name"=> "callback",
            "friendlyName" => "Callback verification token",
            "type" => "text",
            "required" => true
        ]
    ];
}

function Xendit_setWebhookURL(){
    $url = 'https://api.xendit.co/callback_urls/invoice';
    $apiKey = ExtensionHelper::getConfig('Xendit', 'api_key');
    $apiKey = base64_encode($apiKey . ':');
    $data = [
        'url' => 'http://vps.coreware.nl:8000/extensions/xendit/webhook'
    ];
    Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic ' . $apiKey
    ])->post($url, $data);
}
