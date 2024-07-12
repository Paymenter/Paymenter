<?php

namespace App\Extensions\Gateways\Stripe;

use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Stripe extends Gateway
{
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'stripe_secret_key',
                'label' => 'Stripe Secret Key',
                'type' => 'text',
                'description' => 'Find your API keys at https://dashboard.stripe.com/apikeys',
                'required' => true,
            ],
            [
                'name' => 'stripe_webhook_secret',
                'label' => 'Stripe webhook secret',
                'type' => 'text',
                'description' => 'Stripe webhook secret',
                'required' => true,
            ],
            [
                'name' => 'stripe_publishable_key',
                'label' => 'Stripe Publishable Key',
                'type' => 'text',
                'description' => 'Find your API keys at https://dashboard.stripe.com/apikeys',
                'required' => true,
            ],
        ];
    }

    public function pay($invoice, $total)
    {

        $paymentIntent = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('stripe_secret_key'),
        ])->acceptJson()->asForm()->post('https://api.stripe.com/v1/payment_intents', [
            'amount' => $total * 100,
            'currency' => $invoice->currency_code,
            'automatic_payment_methods' => ['enabled' => 'true'],
            'metadata' => ['invoice_id' => $invoice->id],
        ])->object();

        Log::info('Stripe payment intent', (array) $paymentIntent);

        // Pay the invoice using Stripe
        return view('extensions::gateways.stripe.pay', ['invoice' => $invoice, 'total' => $total, 'paymentIntent' => $paymentIntent, 'stripePublishableKey' => $this->config('stripe_publishable_key')]);
    }

    public function webhook(Request $request)
    {
        if (!$this->isValidSignature($request->getContent(), $request->header('Stripe-Signature'), $this->config('stripe_webhook_secret'))) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = json_decode($request->getContent());

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a StripePaymentIntent
                // Get fee from payment intent
                $fee = 0;
                if (isset($paymentIntent->charges->data[0]->balance_transaction)) {
                    $balanceTransaction = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->config('stripe_secret_key'),
                    ])->get('https://api.stripe.com/v1/balance_transactions/' . $paymentIntent->charges->data[0]->balance_transaction)->object();
                    $fee = $balanceTransaction->fee / 100;
                }
                ExtensionHelper::addPayment($paymentIntent->metadata->invoice_id, 'Stripe', $paymentIntent->amount / 100, $fee ?? null, $paymentIntent->id);
                break;
            case 'payment_method.attached':
                $paymentMethod = $event->data->object; // contains a StripePaymentMethod
                break;
                // ... handle other event types
            default:
                // Unexpected event type
                http_response_code(400);
                exit();
        }

        http_response_code(200);
    }

    // Function to split and decode the Stripe-Signature header
    private function getHeaderValues($sig_header)
    {
        $parts = explode(',', $sig_header);
        $timestamp = null;
        $signature = null;

        foreach ($parts as $part) {
            if (strpos($part, 't=') === 0) {
                $timestamp = substr($part, 2);
            } elseif (strpos($part, 'v1=') === 0) {
                $signature = substr($part, 3);
            }
        }

        return [$timestamp, $signature];
    }

    // Validate the signature
    private function isValidSignature($payload, $sig_header, $secret)
    {
        [$timestamp, $signature] = $this->getHeaderValues($sig_header);

        if (empty($timestamp) || empty($signature)) {
            return false;
        }

        // Create the signed payload string
        $signed_payload = $timestamp . '.' . $payload;

        // Compute the expected signature
        $expected_signature = hash_hmac('sha256', $signed_payload, $secret);

        // Compare the expected signature to the actual signature
        return hash_equals($expected_signature, $signature);
    }
}
