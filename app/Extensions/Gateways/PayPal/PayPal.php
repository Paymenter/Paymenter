<?php
namespace App\Extensions\Gateways\PayPal;

use App\Classes\Extensions\Gateway;
use App\Helpers\ExtensionHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PayPal extends Gateway
{
    public function getMetadata()
    {
        return [
            'display_name' => 'PayPal',
            'version' => '1.0.1',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }
    
    public function pay($total, $products, $orderId)
    {
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
            'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret),
        ])->asForm()->post($url . '/v1/oauth2/token', [
            'grant_type' => 'client_credentials',
        ]);

        $token = $response->json()['access_token'];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
            'PayPal-Request-Id' => $orderId,
        ])->post($url . '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $orderId,
                    'amount' => [
                        'currency_code' => ExtensionHelper::getCurrency(),
                        'value' => round($total, 2)
                    ],
                ],
            ],
            'payment_source' => [
                'paypal' => [
                    'experience_context' => [
                        'cancel_url' => route('clients.invoice.show', $orderId),
                        'return_url' => route('clients.invoice.show', $orderId),
                        'brand_name' =>  config('app.name', 'Paymenter'),
                        'shipping_preference'  => 'NO_SHIPPING',
                        'user_action' => 'PAY_NOW',
                        'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                    ]
                ]
            ],
        ]);

        if ($response->failed()) {
            ExtensionHelper::error('PayPal', $response->json());
        }

        return $response->json()['links'][1]['href'];
    }

    public function webhook(Request $request)
    {
        $body = $request->getContent();
        $sigString = $request->header('PAYPAL-TRANSMISSION-ID') . '|' . $request->header('PAYPAL-TRANSMISSION-TIME') . '|' . ExtensionHelper::getConfig('PayPal', 'webhookId') . '|' . crc32($body);
        $pubKey = openssl_pkey_get_public(file_get_contents($request->header('PAYPAL-CERT-URL')));
        $details = openssl_pkey_get_details($pubKey);
        $verifyResult = openssl_verify(
            $sigString,
            base64_decode(
                $request->header('PAYPAL-TRANSMISSION-SIG')
            ),
            $details['key'],
            'sha256WithRSAEncryption'
        );
        if ($verifyResult === 1) {
            $data = json_decode($body, true);
            if ($data['event_type'] == 'CHECKOUT.ORDER.APPROVED') {
                $orderId = $data['resource']['purchase_units'][0]['reference_id'];
                // Capture order
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
                    'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret),
                ])->asForm()->post($url . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);
        
                $token = $response->json()['access_token'];
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])->post($url . '/v2/checkout/orders/' . $data['resource']['id'] . '/capture', [
                    'note_to_payer' => 'Thank you for your payment!',
                ]);

                if ($response->failed()) {
                    ExtensionHelper::error('PayPal', $response->json());
                }

                ExtensionHelper::paymentDone($orderId, 'PayPal', $data['resource']['id']);
            }
        }
    }

    public function getConfig()
    {
        return [
            [
                'name' => 'client_id',
                'type' => 'text',
                'friendlyName' => 'Client ID',
                'required' => true,
            ],
            [
                'name' => 'client_secret',
                'type' => 'text',
                'friendlyName' => 'Client Secret',
                'required' => true,
            ],
            [
                'name' => 'live',
                'type' => 'boolean',
                'friendlyName' => 'Live mode',
                'required' => false,
            ],
            [
                'name' => 'webhookId',
                'type' => 'text',
                'friendlyName' => 'Webhook ID',
                'required' => true,
            ],
        ];
    }
}
