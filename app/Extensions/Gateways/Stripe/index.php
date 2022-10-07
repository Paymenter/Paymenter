<?php

namespace App\Extensions\Gateways\Stripe;

$name = 'Stripe';
$description = 'Stripe Payment Gateway';
$version = '1.0';
$author = 'CorwinDev';
$website = 'http://stripe.com';
$database = 'gateway_stripe';

function stripe_config() {
    $configarray = array(
        "secretkey" => array("FriendlyName" => "Secret Key", "Type" => "text", "Size" => "40", ),
        "publishablekey" => array("FriendlyName" => "Publishable Key", "Type" => "text", "Size" => "40", ),
        "testmode" => array("FriendlyName" => "Test Mode", "Type" => "boolean", ),
    );
    return $configarray;
}

function create ($params) {
    $gateway = new StripeGateway($params);
    return $gateway->create();
}