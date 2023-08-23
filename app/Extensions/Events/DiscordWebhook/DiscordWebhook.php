<?php

namespace App\Extensions\Events\DiscordWebhook;

use App\Classes\Extensions\Event;


class DiscordWebhook extends Event
{

    public function getConfig()
    {
        return [
            [
                'name' => 'webhook_url',
                'type' => 'text',
                'friendlyName' => 'Webhook URL',
                'required' => true,
            ],
        ];
    }
}
