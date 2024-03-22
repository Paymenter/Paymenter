<?php

namespace App\Extensions\Gateways\Mollie;

use App\Classes\Extensions\Gateway;
use App\Helpers\ExtensionHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class Mollie extends Gateway
{
    public function getMetadata()
    {
        return [
            'display_name' => 'Mollie',
            'version' => '1.0.1',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }
    
    public function pay($total, $products, $orderId)
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

    public function webhook(Request $request)
    {
        $url = 'https://api.mollie.com/v2/payments/' . $request->id;
        $client_id = ExtensionHelper::getConfig('Mollie', 'api_key');
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $client_id,
        ])->get($url);
        if ($response->json()['status'] == 'paid') {
            $orderId = $response->json()['metadata']['order_id'];
            ExtensionHelper::paymentDone($orderId, 'Mollie');
        }

        return $response->json();
    }

    public function getConfig()
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
}
