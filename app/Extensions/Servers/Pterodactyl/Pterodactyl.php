<?php

namespace App\Extensions\Servers\Pterodactyl;

use App\Classes\Extension\Server;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

/**
 * Class Pterodactyl
 * @package Paymenter\Extensions\Servers\Pterodactyl
 */
class Pterodactyl extends Server
{
    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'host',
                'label' => 'Pterodactyl URL',
                'type' => 'text',
                'description' => 'Pterodactyl URL',
                'required' => true,
                'validation' => 'url',
            ],
            [
                'name' => 'api_key',
                'label' => 'Pterodactyl API Key',
                'type' => 'text',
                'description' => 'Pterodactyl API Key',
                'required' => true,
            ],
        ];
    }

    public function testConfig(): bool|string
    {
        try {
            $this->request($this->config('host') . '/api/application/servers', 'GET');
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function request($url, $method = 'get', $data = []): array
    {
        // dd($this->config('api_key'), $this->config('host'));    
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('api_key'),
            'Accept' => 'application/json',
        ])->$method($this->config('host') . $url, $data);

        if ($response->status() !== 200) {
            throw new \Exception('Pterodactyl API error: ' . $response->json()['errors'][0]['detail']);
        }

        return $response->json();
    }

    public function getProductConfig($values = []): array
    {
        $nodes =  $this->request('/api/application/nodes');
        $nodeList = [
            '0' => 'None',
        ];
        foreach ($nodes['data'] as $node) {
            $nodeList[$node['attributes']['id']] = $node['attributes']['name'];
        }

        $location =  $this->request('/api/application/locations');
        $locationList = [];
        foreach ($location['data'] as $location) {
            $locationList[$location['attributes']['id']] = $location['attributes']['short'];
        }

        $nests =  $this->request('/api/application/nests');
        $nestList = [];
        foreach ($nests['data'] as $nest) {
            $nestList[$nest['attributes']['id']] = $nest['attributes']['name'];
        }

        $eggList = [];
        if (isset($values['nest_id'])) {
            $eggs =  $this->request('/api/application/nests/' . $values['nest_id'] . '/eggs');
            foreach ($eggs['data'] as $egg) {
                $eggList[$egg['attributes']['id']] = $egg['attributes']['name'];
            }
        }


        return [
            [
                'name' => 'location_id',
                'label' => 'Location',
                'type' => 'select',
                'description' => 'Location',
                'options' => $locationList,
                'required' => true,
                'hint' => new HtmlString('<a href="https://docs.paymenter.org/docs/servers/pterodactyl" target="_blank">Documentation</a>'),
            ],
            [
                'name' => 'node',
                'label' => 'Node',
                'type' => 'select',
                'description' => 'Fill in to install the server on a specific node',
                'options' => $nodeList,
            ],
            [
                'name' => 'nest_id',
                'label' => 'Nest ID',
                'type' => 'select',
                'options' => $nestList,
                'description' => 'Nest ID to fetch the eggs from',
                'required' => true,
                // Lets fetch the eggs every time the nest id changes
                'live' => true,
            ],
            [
                'name' => 'egg_id',
                'label' => 'Egg ID',
                'type' => 'select',
                'options' => $eggList,
                'required' => true,
            ],

        ];
    }
}
