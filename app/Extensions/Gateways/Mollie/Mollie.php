<?php

namespace App\Extensions\Gateways\Mollie;

use Stripe\StripeClient;
use App\Helpers\ExtensionHelper;
use App\Models\Extension;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class Mollie
{
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
