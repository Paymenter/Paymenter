<?php

namespace App\Extensions\Servers\Pterodactyl;

use App\Classes\Extensions\Server;
use App\Helpers\ExtensionHelper;
use Illuminate\Support\Facades\Http;

class Pterodactyl extends Server
{

    private function config($key)
    {
        $config = ExtensionHelper::getConfig('Pterodactyl', $key);
        if ($config) {
            return $config;
        }

        return null;
    }

    public function getConfig()
    {
        return [
            [
                'name' => 'host',
                'friendlyName' => 'Pterodactyl panel url',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'apiKey',
                'friendlyName' => 'API Key',
                'type' => 'text',
                'required' => true,
            ],
        ];
    }


    private function postRequest($url, $data)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('apiKey'),
            'Accept' => 'Application/vnd.Pterodactyl.v1+json',
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        return $response;
    }

    private function getRequest($url)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('apiKey'),
            'Accept' => 'Application/vnd.Pterodactyl.v1+json',
            'Content-Type' => 'application/json',
        ])->get($url);

        return $response;
    }

    public function deleteRequest($url)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('apiKey'),
            'Accept' => 'Application/vnd.Pterodactyl.v1+json',
            'Content-Type' => 'application/json',
        ])->delete($url);

        return $response;
    }

    public function getProductConfig($options)
    {
        $nodes =  $this->getRequest($this->config('host') . '/api/application/nodes');
        $nodeList = [
            [
                'name' => 'None',
                'value' => '',
            ],
        ];
        foreach ($nodes->json()['data'] as $node) {
            $nodeList[] = [
                'name' => $node['attributes']['name'],
                'value' => $node['attributes']['id'],
            ];
        }

        $location =  $this->getRequest($this->config('host') . '/api/application/locations');
        $locationList = [];
        foreach ($location->json()['data'] as $location) {
            $locationList[] = [
                'name' => $location['attributes']['short'],
                'value' => $location['attributes']['id'],
            ];
        }

        $nests =  $this->getRequest($this->config('host') . '/api/application/nests');
        $nestList = [];
        foreach ($nests->json()['data'] as $nest) {
            $nestList[] = [
                'name' => $nest['attributes']['name'],
                'value' => $nest['attributes']['id'],
            ];
        }



        return [
            [
                'name' => 'node',
                'friendlyName' => 'Pterodactyl Node (leave empty for node assigned to location)',
                'type' => 'dropdown',
                'options' => $nodeList,
            ],
            [
                'name' => 'location',
                'friendlyName' => 'Pterodactyl Location',
                'type' => 'dropdown',
                'options' => $locationList,
                'required' => true,
            ],
            [
                'name' => 'egg',
                'friendlyName' => 'Pterodactyl Egg ID',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'nest',
                'friendlyName' => 'Pterodactyl Nest',
                'type' => 'dropdown',
                'options' => $nestList,
                'required' => true,
            ],
            [
                'name' => 'memory',
                'friendlyName' => 'Pterodactyl Memory',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'swap',
                'friendlyName' => 'Pterodactyl Swap',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'disk',
                'friendlyName' => 'Pterodactyl Disk',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'io',
                'friendlyName' => 'Pterodactyl IO',
                'type' => 'text',
                'required' => true,
                'description' => 'IO is a number between 10 and 1000. 500 is the default value.',
            ],
            [
                'name' => 'cpu',
                'friendlyName' => 'CPU limit',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'databases',
                'friendlyName' => 'Pterodactyl Database',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'backups',
                'friendlyName' => 'Pterodactyl Backups',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'skip_scripts',
                'friendlyName' => 'Pterodactyl Skip Scripts',
                'type' => 'boolean',
                'description' => 'Decides if Pterodactyl will skip install scripts',
            ],
            [
                'name' => 'allocation',
                'friendlyName' => 'Pterodactyl Allocation',
                'type' => 'text',
                'required' => true,
                'description' => 'How many ports the user can allocate. Must be at least one.',
            ],
        ];
    }

    public function createServer($user, $parmas, $order, $product, $configurableOptions)
    {
        if ($this->serverExists($product->id)) {
            ExtensionHelper::error('Pterodactyl', 'Server already exists for order ' . $product->id);

            return;
        }
        $url = $this->config('host') . '/api/application/servers';
        $nest_id = isset($configurableOptions['nest_id']) ? $configurableOptions['nest_id'] : $parmas['nest'];
        $egg_id = isset($configurableOptions['egg']) ? $configurableOptions['egg'] : $parmas['egg'];
        $eggData = $this->getRequest($this->config('host') . '/api/application/nests/' . $nest_id . '/eggs/' . $egg_id . '?include=variables')->json();
        if (!isset($eggData['attributes'])) {
            ExtensionHelper::error('Pterodactyl', 'No egg data found for ' . $parmas['egg']);

            return;
        }
        $environment = [];
        foreach ($eggData['attributes']['relationships']['variables']['data'] as $key => $val) {
            $attr = $val['attributes'];
            $var = $attr['env_variable'];
            $default = $attr['default_value'];
            // If the variable is configurable, get the value from the configurable options
            if (isset($configurableOptions[$var])) {
                $default = $configurableOptions[$var];
            }
            $environment[$var] = $default;
        }
        $cpu = isset($configurableOptions['cpu']) ? $configurableOptions['cpu'] : $parmas['cpu'];
        $io = isset($configurableOptions['io']) ? $configurableOptions['io'] : $parmas['io'];
        $disk = isset($configurableOptions['disk']) ? $configurableOptions['disk'] : $parmas['disk'];
        $swap = isset($configurableOptions['swap']) ? $configurableOptions['swap'] : $parmas['swap'];
        $memory = isset($configurableOptions['memory']) ? $configurableOptions['memory'] : $parmas['memory'];
        $allocations = isset($configurableOptions['allocation']) ? $configurableOptions['allocation'] : $parmas['allocation'];
        $location = isset($configurableOptions['location']) ? $configurableOptions['location'] : $parmas['location'];
        $databases = isset($configurableOptions['databases']) ? $configurableOptions['databases'] : $parmas['databases'];
        $backups = isset($configurableOptions['backups']) ? $configurableOptions['backups'] : $parmas['backups'];
        $startup = isset($configurableOptions['startup']) ? $configurableOptions['startup'] : $eggData['attributes']['startup'];
        $node = isset($configurableOptions['node']) ? $configurableOptions['node'] : $parmas['node'];

        if ($node) {
            $allocation = $this->getRequest($this->config('host') . '/api/application/nodes/' . $parmas['node'] . '/allocations');
            $allocation = $allocation->json();
            foreach ($allocation['data'] as $key => $val) {
                if ($val['attributes']['assigned'] == false) {
                    $allocation = $val['attributes']['id'];
                    break;
                }
            }
            $json = [
                'name' => $this->random_string(8) . '-' . $product->id,
                'user' => (int) $this->getUser($user),
                'egg' => (int) $egg_id,
                'docker_image' => $eggData['attributes']['docker_image'],
                'startup' => $startup,
                'limits' => [
                    'memory' => (int) $memory,
                    'swap' => (int) $swap,
                    'disk' => (int) $disk,
                    'io' => (int) $io,
                    'cpu' => (int) $cpu,
                ],
                'feature_limits' => [
                    'databases' => $databases ? (int) $databases : null,
                    'allocations' => $allocations,
                    'backups' => $backups,
                ],
                'allocation' => [
                    'default' => (int) $allocation,
                ],
                'environment' => $environment,
                'external_id' => (string) $product->id,
            ];
        } else {
            $json = [
                'name' => $this->random_string(8) . '-' . $product->id,
                'user' => (int) $this->getUser($user),
                'egg' => (int) $egg_id,
                'docker_image' => $eggData['attributes']['docker_image'],
                'startup' => $startup,
                'limits' => [
                    'memory' => (int) $memory,
                    'swap' => (int) $swap,
                    'disk' => (int) $disk,
                    'io' => (int) $io,
                    'cpu' => (int) $cpu,
                ],
                'feature_limits' => [
                    'databases' => $databases ? (int) $databases : null,
                    'allocations' => $allocations,
                    'backups' => $backups,
                ],
                'deploy' => [
                    'locations' => [(int) $location],
                    'dedicated_ip' => false,
                    'port_range' => [],
                ],
                'environment' => $environment,
                'external_id' => (string) $product->id,
            ];
        }
        $response = $this->postRequest($url, $json);

        if (!$response->successful()) {
            ExtensionHelper::error('Pterodactyl', 'Failed to create server for order ' . $product->id . ' with error ' . $response->body());

            return;
        }

        return true;
    }

    private function random_string($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function getUser($user)
    {
        $url = $this->config('host') . '/api/application/users?filter%5Bemail%5D=' . $user->email;
        $response = $this->getRequest($url);
        $users = $response->json();
        if (count($users['data']) > 0) {
            return $users['data'][0]['attributes']['id'];
        } else {
            $url = $this->config('host') . '/api/application/users';
            $json = [
                'username' => $this->random_string(8),
                'email' => $user->email,
                'first_name' => $user->name,
                'last_name' => 'User',
            ];
            $response = $this->postRequest($url, $json);
            if (!$response->successful()) {
                ExtensionHelper::error('Pterodactyl', 'Failed to create user for order ' . $product->id . ' with error ' . $response->body());
            }
            $user = $response->json();

            return $user['attributes']['id'];
        }
    }

    private function serverExists($order)
    {
        $url = $this->config('host') . '/api/application/servers/external/' . $order;
        $response = $this->getRequest($url);
        $code = $response->status();
        if ($code == 200) {
            return $response->json()['attributes']['id'];
        }

        return false;
    }

    public function suspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $server = $this->serverExists($product->id);
        if ($server) {
            $url = $this->config('host') . '/api/application/servers/' . $server . '/suspend';
            $this->postRequest($url, []);

            return true;
        }

        return false;
    }

    public function unsuspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $server = $this->serverExists($product->id);
        if ($server) {
            $url = $this->config('host') . '/api/application/servers/' . $server . '/unsuspend';
            $this->postRequest($url, []);

            return true;
        }

        return false;
    }

    public function terminateServer($user, $params, $order, $product, $configurableOptions)
    {
        $server = $this->serverExists($product->id);
        if ($server) {
            $url = $this->config('host') . '/api/application/servers/' . $server;
            $this->deleteRequest($url);

            return true;
        }

        return false;
    }

    public function getLink($user, $params, $order, $product)
    {
        $server = $this->serverExists($product->id);
        if ($server) {
            $url = $this->config('host') . '/api/application/servers/' . $server;
            $response = $this->getRequest($url);
            $server = $response->json();

            return $this->config('host') . '/server/' . $server['attributes']['identifier'];
        }

        return false;
    }
}
