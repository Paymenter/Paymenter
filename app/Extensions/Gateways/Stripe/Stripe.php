<?php

namespace App\Extensions\Gateways\Stripe;

use Stripe\StripeClient;
use App\Helpers\ExtensionHelper;
use App\Models\Extension;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class Stripe
{
    public function getConfig()
    {
        return [
            [
                'name' => 'stripe_secret_key',
                'label' => 'Stripe Secret Key',
                'type' => 'text',
                'description' => 'Stripe secret key',
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
