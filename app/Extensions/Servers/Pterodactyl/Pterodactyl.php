<?php

namespace App\Extensions\Servers\Pterodactyl;

use App\Classes\Extensions\Server;
use App\Helpers\ExtensionHelper;
use Illuminate\Support\Facades\Http;

class Pterodactyl extends Server
{
    public function getMetadata(): array
    {
        return [
            'display_name' => 'Pterodactyl',
            'version' => '1.0.0',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }
    
    private function config($key): ?string
    {
        $config = ExtensionHelper::getConfig('Pterodactyl', $key);
        if ($config) {
            return $config;
        }

        return null;
    }

    public function getConfig(): array
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


    private function postRequest($url, $data): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('apiKey'),
            'Accept' => 'Application/vnd.Pterodactyl.v1+json',
            'Content-Type' => 'application/json',
        ])->post($url, $data);
    }

    private function getRequest($url): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('apiKey'),
            'Accept' => 'Application/vnd.Pterodactyl.v1+json',
            'Content-Type' => 'application/json',
        ])->get($url);
    }

    public function deleteRequest($url): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('apiKey'),
            'Accept' => 'Application/vnd.Pterodactyl.v1+json',
            'Content-Type' => 'application/json',
        ])->delete($url);
    }

    public function getProductConfig($options): array
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
                'name' => 'cpu_pinning',
                'friendlyName' => 'CPU pinning',
                'type' => 'text',
                'required' => false,
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
                'name' => 'servername',
                'friendlyName' => 'Product Name (leave empty for auto generated name)',
                'type' => 'text',
                'required' => false,
                'description' => 'If you do not fill in this field the server name will be set to the PRODUCT NAME #ID eg. "Test Product #26"',
            ],
            [
                'name' => 'allocation',
                'friendlyName' => 'Pterodactyl Allocation',
                'type' => 'text',
                'required' => true,
                'description' => 'How many ports the user can allocate. Must be at least one.',
            ],
            [
                'name' => 'skip_scripts',
                'friendlyName' => 'Pterodactyl Skip Scripts',
                'type' => 'boolean',
                'description' => 'Decides if Pterodactyl will skip install scripts',
            ],
        ];
    }

    public function createServer($user, $params, $order, $orderProduct, $configurableOptions): bool
    {
        if ($this->serverExists($orderProduct->id)) {
            ExtensionHelper::error('Pterodactyl', 'Server already exists for order ' . $orderProduct->id);

            return true;
        }

        $url = $this->config('host') . '/api/application/servers';
        $nest_id = $configurableOptions['nest_id'] ?? $params['nest'];
        $egg_id = $configurableOptions['egg'] ?? $params['egg'];
        $eggData = $this->getRequest($this->config('host') . '/api/application/nests/' . $nest_id . '/eggs/' . $egg_id . '?include=variables')->json();

        if (!isset($eggData['attributes'])) {
            ExtensionHelper::error('Pterodactyl', 'No egg data found for ' . $params['egg']);

            return false;
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

        $cpu = $configurableOptions['cpu'] ?? $params['cpu'];
        $cpu_pinning = $configurableOptions['cpu_pinning'] ?? $params['cpu_pinning'] ?? null;
        $io = $configurableOptions['io'] ?? $params['io'];
        $disk = $configurableOptions['disk'] ?? $params['disk'];
        $swap = $configurableOptions['swap'] ?? $params['swap'];
        $memory = $configurableOptions['memory'] ?? $params['memory'];
        $allocations = $configurableOptions['allocation'] ?? $params['allocation'];
        $location = $configurableOptions['location'] ?? $params['location'];
        $databases = $configurableOptions['databases'] ?? $params['databases'];
        $backups = $configurableOptions['backups'] ?? $params['backups'];
        $startup = $configurableOptions['startup'] ?? $eggData['attributes']['startup'];
        $node = $configurableOptions['node'] ?? $params['node'];
        $servername = $configurableOptions['servername'] ?? $params['servername'] ?? false;
        $servername = empty($servername) ? $orderProduct->product->name . ' #' . $orderProduct->id : $servername;

        if ($node) {
            $allocation = $this->getRequest($this->config('host') . '/api/application/nodes/' . $params['node'] . '/allocations');
            $allocation = $allocation->json();
            foreach ($allocation['data'] as $key => $val) {
                if (!$val['attributes']['assigned']) {
                    $allocation = $val['attributes']['id'];
                    break;
                }
            }
            $json = [
                'name' => $servername,
                'user' => (int) $this->getUser($user, $orderProduct),
                'egg' => (int) $egg_id,
                'docker_image' => $eggData['attributes']['docker_image'],
                'startup' => $startup,
                'limits' => [
                    'memory' => (int) $memory,
                    'swap' => (int) $swap,
                    'disk' => (int) $disk,
                    'io' => (int) $io,
                    'cpu' => (int) $cpu,
                    'threads' => $cpu_pinning,
                ],
                'feature_limits' => [
                    'databases' => $databases ? (int) $databases : null,
                    'allocations' => (int) $allocations,
                    'backups' => (int) $backups,
                ],
                'allocation' => [
                    'default' => (int) $allocation,
                ],
                'environment' => $environment,
                'external_id' => (string) $orderProduct->id,
            ];
        } else {
            $json = [
                'name' => $servername,
                'user' => (int) $this->getUser($user, $orderProduct),
                'egg' => (int) $egg_id,
                'docker_image' => $eggData['attributes']['docker_image'],
                'startup' => $startup,
                'limits' => [
                    'memory' => (int) $memory,
                    'swap' => (int) $swap,
                    'disk' => (int) $disk,
                    'io' => (int) $io,
                    'cpu' => (int) $cpu,
                    'threads' => $cpu_pinning,
                ],
                'feature_limits' => [
                    'databases' => $databases ? (int) $databases : null,
                    'allocations' => (int) $allocations,
                    'backups' => (int) $backups,
                ],
                'deploy' => [
                    'locations' => [(int) $location],
                    'dedicated_ip' => false,
                    'port_range' => [],
                ],
                'environment' => $environment,
                'external_id' => (string) $orderProduct->id,
            ];
        }
        $response = $this->postRequest($url, $json);

        if (!$response->successful()) {
            ExtensionHelper::error('Pterodactyl', 'Failed to create server for order ' . $orderProduct->id . ' with error ' . $response->body());

            return false;
        }

        return true;
    }

    private function random_string($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function getUser($user, $product = null)
    {
        $url = $this->config('host') . '/api/application/users?filter%5Bemail%5D=' . $user->email;
        $response = $this->getRequest($url);
        $users = $response->json();
        if (count($users['data']) > 0) {
            return $users['data'][0]['attributes']['id'];
        } else {
            $url = $this->config('host') . '/api/application/users';
            $sanitized = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($user->name));
            if (empty($sanitized)) {
                $sanitized = $this->random_string(8); // Ta funkcja musi być dostępna w twoim kodzie
            }
            $json = [
                'username' => $sanitized.'_'.$this->random_string(3)??$this->random_string(8),
                'email' => $user->email,
                'first_name' => $user->name,
                'last_name' => $user->lastname??'User',
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

    public function suspendServer($user, $params, $order, $orderProduct, $configurableOptions): bool
    {
        $server = $this->serverExists($orderProduct->id);
        if ($server) {
            $url = $this->config('host') . '/api/application/servers/' . $server . '/suspend';
            $this->postRequest($url, []);

            return true;
        }

        return false;
    }

    public function unsuspendServer($user, $params, $order, $orderProduct, $configurableOptions): bool
    {
        $server = $this->serverExists($orderProduct->id);
        if ($server) {
            $url = $this->config('host') . '/api/application/servers/' . $server . '/unsuspend';
            $this->postRequest($url, []);

            return true;
        }

        return false;
    }

    public function terminateServer($user, $params, $order, $orderProduct, $configurableOptions): bool
    {
        $server = $this->serverExists($orderProduct->id);
        if ($server) {
            $url = $this->config('host') . '/api/application/servers/' . $server;
            $this->deleteRequest($url);

            return true;
        }

        return false;
    }

    public function getLink($user, $params, $order, $orderProduct): bool|string
    {
        $server = $this->serverExists($orderProduct->id);
        if ($server) {
            $url = $this->config('host') . '/api/application/servers/' . $server;
            $response = $this->getRequest($url);
            $server = $response->json();

            return $this->config('host') . '/server/' . $server['attributes']['identifier'];
        }

        return false;
    }
}
