<?php

use Stripe\StripeClient;

include(app_path() . '/Helpers/Extension.php');

$name = 'Stripe';
$description = 'Stripe Payment Gateway';
$version = '1.0';
$author = 'CorwinDev';
$website = 'http://stripe.com';
$database = 'gateway_stripe';

function create($request)
{
    $client = StripeClient();

    $order = $client->checkout->sessions->create([
        'line_items' => [
            [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Test Product',
                        'description' => 'Test Product Description',
                    ],
                    'unit_amount_decimal' => 100,
                ],
                'quantity' => 1,
            ]
        ],
        'mode' => 'payment',
        "payment_method_types" => ["card"],
        'success_url' => url('/success'),
        'cancel_url' => url('/cancel'),
    ]);
    error_log($order->url);
    // ToDo: make success_url and cancel_url dynamic
    return $order;
}

function webhook($request)
{
    create($request);
}

function stripeClient()
{
    return new \Stripe\StripeClient(Extension::getConfig("Stripe", 'stripe_test_key'));
}
