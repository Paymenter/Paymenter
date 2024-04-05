<?php

namespace App\Extensions\Gateways\Stripe;

use App\Classes\Extensions\Gateway;
use Stripe\StripeClient;
use App\Helpers\ExtensionHelper;
use App\Models\Extension;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class Stripe extends Gateway
{
    public function getMetadata()
    {
        return [
            'display_name' => 'Stripe',
            'version' => '2.0.0',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }

    public function getUrl($_, $products, $orderId, $client)
    {
        // Create array with all the products
        $items = [];
        foreach ($products as $product) {
            $items[] = [
                'price_data' => [
                    'currency' => ExtensionHelper::getCurrency(),
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => round($product->price / $product->quantity * 100, 0),
                ],
                'quantity' => $product->quantity,
            ];
        }
        $order = $client->checkout->sessions->create([
            'line_items' => $items,
            'currency' => ExtensionHelper::getCurrency(),
            'mode' => 'payment',
            'success_url' => route('clients.invoice.show', $orderId),
            'cancel_url' => route('clients.invoice.show', $orderId),
            'customer_email' => auth()->user()->email,
            'customer_creation' => 'always',
            'metadata' => [
                'user_id' => auth()->user()->id,
                'order_id' => $orderId,
            ],
        ]);


        return $order;
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('stripe-signature');
        $endpoint_secret = ExtensionHelper::getConfig('Stripe', 'stripe_webhook_secret');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit;
        }
        if ($event->type == 'checkout.session.completed') {
            if ($event->data->object->mode == 'payment') {
                $order = $event->data->object;
                $order_id = $order->metadata->order_id;
                ExtensionHelper::paymentDone($order_id, 'Stripe', $order->payment_intent);
            }
        }
        if (class_exists('App\Extensions\Gateways\StripeSubscriptions\StripeSubscriptions')) {
            (new \App\Extensions\Gateways\StripeSubscriptions\StripeSubscriptions(Extension::where('name', 'StripeSubscriptions')->first()))->webhook($event);
        }

        return response()->json(['success' => true]);
    }

    public function stripeClient()
    {
        if (!ExtensionHelper::getConfig('Stripe', 'stripe_test_mode')) {
            $stripe = new StripeClient(
                ExtensionHelper::getConfig('Stripe', 'stripe_secret_key')
            );
        } else {
            $stripe = new StripeClient(
                ExtensionHelper::getConfig('Stripe', 'stripe_test_key')
            );
        }

        return $stripe;
    }

    public function pay($total, $products, $orderId)
    {
        $stripe = $this->stripeClient();
        $order = $this->getUrl($total, $products, $orderId, $stripe);

        return $stripe->checkout->sessions->retrieve($order->id, [])->url;
    }

    public function getConfig()
    {
        return [
            [
                'name' => 'stripe_secret_key',
                'friendlyName' => 'Stripe Secret Key',
                'type' => 'text',
                'description' => 'Stripe secret key',
                'required' => true,
            ],
            [
                'name' => 'stripe_webhook_secret',
                'friendlyName' => 'Stripe webhook secret',
                'type' => 'text',
                'description' => 'Stripe webhook secret',
                'required' => true,
            ],
            [
                'name' => 'stripe_test_mode',
                'friendlyName' => 'Stripe test mode',
                'type' => 'boolean',
                'description' => 'Stripe test mode',
                'required' => false,
            ],
            [
                'name' => 'stripe_test_key',
                'friendlyName' => 'Stripe test key',
                'type' => 'text',
                'description' => 'Stripe test key',
                'required' => false,
            ],
        ];
    }
}
