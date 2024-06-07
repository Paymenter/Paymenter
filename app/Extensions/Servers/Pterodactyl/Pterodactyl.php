<?php
namespace App\Extensions\Servers\Pterodactyl;

use App\Classes\Extension\Server;

/**
 * Class Pterodactyl
 * @package Paymenter\Extensions\Servers\Pterodactyl
 */ 
class Pterodactyl extends Server
{
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'pterodactyl_url',
                'label' => 'Pterodactyl URL',
                'type' => 'text',
                'description' => 'Pterodactyl URL',
                'required' => true,
                'validation' => 'url',
            ],
            [
                'name' => 'pterodactyl_api_key',
                'label' => 'Pterodactyl API Key',
                'type' => 'text',
                'description' => 'Pterodactyl API Key',
                'required' => true,
            ],
        ];
    }
}