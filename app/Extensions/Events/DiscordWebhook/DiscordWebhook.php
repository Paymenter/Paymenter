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
            [
                'name' => 'ping_type',
                'type' => 'dropdown',
                'friendlyName' => 'Ping Type',
                'description' => 'The type of user/role to ping',
                'required' => false,
                'options' => [
                    [
                        'name' => 'None',
                        'value' => 'none',
                    ],
                    [
                        'name' => 'User',
                        'value' => 'user',
                    ],
                    [
                        'name' => 'Role',
                        'value' => 'role',
                    ],
                ],
            ],
            [
                'name' => 'ping_id',
                'type' => 'text',
                'friendlyName' => 'Ping ID',
                'description' => 'The ID of the user/role to ping',
                'required' => false,
            ],
        ];
    }
}
