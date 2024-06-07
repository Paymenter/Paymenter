<?php

namespace App\Extensions\Gateways\Stripe;

use App\Classes\Extension\Gateway;
use Stripe\StripeClient;
use App\Helpers\ExtensionHelper;
use App\Models\Extension;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


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
                'name' => 'stripe_test_key',
                'label' => 'Stripe test key',
                'type' => 'text',
                'description' => 'Stripe test key',
                'required' => false,
            ],
        ];
    }
}
