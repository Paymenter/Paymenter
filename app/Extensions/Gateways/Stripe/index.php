<?php

use Stripe\StripeClient;
use App\Helpers\ExtensionHelper;
use App\Models\Products;

$name = 'Stripe';
$description = 'Stripe Payment Gateway';
$version = '1.0';
$author = 'CorwinDev';
$website = 'http://stripe.com';
$database = 'gateway_stripe';

function getUrl($products, $order)
{
    $client = StripeClient();
    // Create array with all the products
    $products = ExtensionHelper::getProducts();
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
    // Create session
    $order = $client->checkout->sessions->create([
        'line_items' => $items,
        'mode' => 'payment',
        "payment_method_types" => ["card", "ideal"],
        'success_url' => route('checkout.success'),
        'cancel_url' => route('checkout.cancel'),
        'customer_email' => auth()->user()->email,
        'metadata' => [
            'user_id' => auth()->user()->id,
            'order_id' => $order,
        ],
    ]);

    return $order;
}

function webhook($request)
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
        error_log($order);
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


function pay($total, $products, $orderId)
{
    $stripe = stripeClient();
    $order = getUrl($products, $orderId);
    return $stripe->checkout->sessions->retrieve($order->id, []);
}