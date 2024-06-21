<?php

namespace App\Extensions\Gateways\Mollie;

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
