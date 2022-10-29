<?php

use Stripe\StripeClient;
use App\Helpers\ExtensionHelper;

function Stripe_getUrl($products, $orderId)
{
    $client = StripeClient();
    // Create array with all the products
    $items = [];
    foreach ($products as $product) {
        $items[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $product->name,
                ],
                'unit_amount' => $product->price * 100,
            ],
            'quantity' => $product->quantity,
        ];
    }
    $order = $client->checkout->sessions->create([
        'line_items' => $items,
        'mode' => 'payment',
        'success_url' => route('invoice.show', $orderId),
        'cancel_url' => route('invoice.show', $orderId),
        'customer_email' => auth()->user()->email,
        'metadata' => [
            'user_id' => auth()->user()->id,
            'order_id' => $orderId,
        ],
    ]);

    return $order;
}

function Stripe_webhook($request)
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
        exit();
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        // Invalid signature
        http_response_code(400);
        exit();
    }
    if ($event->type == 'checkout.session.completed') {
        $order = $event->data->object;
        $order_id = $order->metadata->order_id;
        ExtensionHelper::paymentDone($order_id);
    }
}

function stripeClient()
{
    if (!ExtensionHelper::getConfig('Stripe', 'stripe_test_mode')) {
        $stripe = new StripeClient(
            ExtensionHelper::getConfig('Stripe', 'stripe_live_secret_key')
        );
    } else {
        $stripe = new StripeClient(
            ExtensionHelper::getConfig('Stripe', 'stripe_test_key')
        );
    }
    return $stripe;
}


function Stripe_pay($total, $products, $orderId)
{
    $stripe = stripeClient();
    $order = Stripe_getUrl($products, $orderId);
    return $stripe->checkout->sessions->retrieve($order->id, []);
}