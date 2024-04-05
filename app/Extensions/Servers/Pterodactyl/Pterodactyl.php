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
            'version' => '1.2.1',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }

    private function config($key): ?string
    {
        $config = ExtensionHelper::getConfig('Pterodactyl', $key);
        if ($config) {
            if ($key == 'host') {
                return rtrim($config, '/');
            }
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

    private function patchRequest($url, $data): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('apiKey'),
            'Accept' => 'Application/vnd.Pterodactyl.v1+json',
            'Content-Type' => 'application/json',
        ])->patch($url, $data);
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
            [
                'name' => 'port_range',
                'friendlyName' => 'Pterodactyl Port Range',
                'type' => 'text',
                'required' => false,
                'description' => 'Port range for the server. Example: 7777-7779',
            ],
            [
                'name' => 'port_array',
                'friendlyName' => 'Pterodactyl Port Array',
                'type' => 'text',
                'required' => false,
                'description' => 'List of ports + their egg variable name. Example: {"SERVER_PORT": 7777, "NONE": 7778, "QUERY_PORT": 27015, "RCON_PORT": 27020}',
            ]
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
        $port_range = $configurableOptions['port_range'] ?? $params['port_range'] ?? null;
        $port_array = $configurableOptions['port_array'] ?? $params['port_array'] ?? null;
        $servername = $configurableOptions['servername'] ?? $params['servername'] ?? false;
        $servername = empty($servername) ? $orderProduct->product->name . ' #' . $orderProduct->id : $servername;
        $port_array = (object) $this->portArrays($port_array, $environment, $location, $node, $orderProduct);
        $default = $port_array->default ?? null;
        $allocationed = $port_array->allocations ?? [];
        $environment = $port_array->environment ?? $environment;

        if ($node) {
            $allocationss = $this->getRequest($this->config('host') . '/api/application/nodes/' . $node . '/allocations');
            $allocationss = $allocationss->json();
            while (!isset($allocation)) {
                foreach ($allocationss['data'] as $key => $val) {
                    if (!$val['attributes']['assigned']) {
                        $allocation = $val['attributes']['id'];
                        break;
                    }
                }
                if (!isset($allocation)) {
                    if (!isset($allocationss['meta']['pagination']['links']['next'])) {
                        ExtensionHelper::error('Pterodactyl', 'Failed to find allocation for order ' . $orderProduct->id . ' skipping server creation');
                        return false;
                    }
                    $allocationss = $this->getRequest($allocationss['meta']['pagination']['links']['next']);
                    $allocationss = $allocationss->json();
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
                    'default' => isset($default) ? (int) $default : $allocation,
                    'additional' => $allocationed ?? [],
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
                'allocation' => [
                    'default' => isset($port_array->default) ? (int)  $port_array->default : null,
                    'additional' => isset($allocationed) ? $allocationed : [],
                ],
                'environment' => $environment,
                'external_id' => (string) $orderProduct->id,
            ];

            if (!$allocationed && !isset($port_range->default)) {
                $json['deploy'] =  [
                    'locations' => [(int) $location],
                    'dedicated_ip' => false,
                    'port_range' => $port_range ? [$port_range] : [],
                ];
            }
        }
        $response = $this->postRequest($url, $json);

        if (!$response->successful()) {
            ExtensionHelper::error('Pterodactyl', 'Failed to create server for order ' . $orderProduct->id . ' with error ' . $response->body());

            return false;
        }

        return true;
    }

    private function portArrays($port_array, $environment, $location, $node, $orderProduct)
    {
        // example {"SERVER_PORT": 7777, "NONE": [7778, 7779] "QUERY_PORT": 2701, "RCON_PORT": 27020}
        if (!isset($port_array)) return [];
        try {
            $port_array = json_decode($port_array, true);
        } catch (\Exception $e) {
            ExtensionHelper::error('Pterodactyl', 'Failed to decode port array for order ' . $orderProduct->id . ' with error ' . $e->getMessage());
            return [];
        }
        if (!$port_array) return [];
        if (!is_array($port_array)) return [];
        if (!$node) {
            // If no node is selected, we need to get the node id from the location
            $node = $this->getRequest($this->config('host') . '/api/application/nodes?per_page=100');
            $node = $node->json();
            $node = collect($node['data'])->where('attributes.location_id', $location);
            while (!$node) {
                if (!isset($node['meta']['pagination']['links']['next'])) {
                    ExtensionHelper::error('Pterodactyl', 'Failed to find node for order ' . $orderProduct->id . ' skipping port array');
                    return [];
                }
                $node = $this->getRequest($node['meta']['pagination']['links']['next']);
                $node = $node->json();
                $node = collect($node['data'])->where('attributes.location_id', $location);
            }
            // Search for the emptiest node
            foreach ($node as $key => $val) {
                if ($val['attributes']['maintenance_mode']) continue;
                // Node has *infinity* resources
                if ($val['attributes']['memory_overallocate'] == -1 && $val['attributes']['disk_overallocate'] == -1) {
                    $node = $val['attributes']['id'];
                    break;
                }
                if (($val['attributes']['memory'] / 100 * ($val['attributes']['memory_overallocate'] + 100)) > $val['attributes']['allocated_resources']['memory']) {
                    $node = $val['attributes']['id'];
                    break;
                }
            }
        }
        if (!$node) {
            ExtensionHelper::error('Pterodactyl', 'Failed to find node for order ' . $orderProduct->id . ' skipping port array');
            return [];
        }

        $availableAllocations = [];
        $allocationData = $this->getRequest($this->config('host') . '/api/application/nodes/' . $node . '/allocations?per_page=100');
        $allocationData = $allocationData->json();

        while (isset($allocationData['meta']['pagination']['links']['next'])) {
            foreach ($allocationData['data'] as $key => $val) {
                if ($val['attributes']['assigned'] == false) {
                    $availableAllocations[] = [
                        'id' => $val['attributes']['id'],
                        'port' => $val['attributes']['port']
                    ];
                }
            }
            $allocationData = $this->getRequest($allocationData['meta']['pagination']['links']['next'] . '&per_page=100');
            $allocationData = $allocationData->json();
        }

        foreach ($allocationData['data'] as $key => $val) {
            if ($val['attributes']['assigned'] == false) {
                $availableAllocations[] = [
                    'id' => $val['attributes']['id'],
                    'port' => $val['attributes']['port']
                ];
            }
        }

        $availableAllocations = collect($availableAllocations);
        $allocations = [];

        foreach ($port_array as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $key2 => $val2) {
                    $allocation = $availableAllocations->where('port', $val2)->first();
                    while (!$allocation) {
                        // Check if there are even ports higher than the one we are looking for
                        if (!$availableAllocations->where('port', '>', $val2)->first()) {
                            // Just pick the first one
                            $allocation = $availableAllocations->random();
                        } else {
                            $allocation = $availableAllocations->where('port', $val2 + 1)->first();
                            $val2++;
                        }
                    }
                    $allocations[] = $allocation['id'];
                    $availableAllocations->forget($availableAllocations->search($allocation));
                    if ($key !== 'NONE' || $key !== 'SERVER_PORT') {
                        if (isset($environment[$key])) $environment[$key] = $allocation['port'];
                    }
                }
                continue;
            }
            // Check if port is available
            $allocation = $availableAllocations->where('port', $val)->first();
            while (!$allocation) {
                // Check if there are even ports higher than the one we are looking for
                if (!$availableAllocations->where('port', '>', $val)->first()) {
                    // Just pick the first one
                    $allocation = $availableAllocations->random();
                } else {
                    $allocation = $availableAllocations->where('port', $val + 1)->first();
                    $val++;
                }
            }
            if ($key !== 'SERVER_PORT') $allocations[] = $allocation['id'];
            $availableAllocations->forget($availableAllocations->search($allocation));

            if ($key !== 'NONE' && $key !== 'SERVER_PORT') {
                if (isset($environment[$key])) $environment[$key] = $allocation['port'];
            }
            if ($key === 'SERVER_PORT') {
                $default = $allocation['id'];
            }
        }

        return [
            'allocations' => $allocations,
            'default' => $default ?? null,
            'environment' => $environment,
        ];
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
                'username' => $sanitized . '_' . $this->random_string(3) ?? $this->random_string(8),
                'email' => $user->email,
                'first_name' => $user->name,
                'last_name' => $user->lastname ?? 'User',
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

    
    public function upgradeServer($user, $params, $order, $orderProduct, $configurableOptions): bool
    {
        $serverId = $this->serverExists($orderProduct->id);
        if (!$serverId) {
            ExtensionHelper::error('Pterodactyl', 'Server does not exist for order ' . $orderProduct->id);

            return false;
        }
        $url = $this->config('host') . '/api/application/servers/external/' . $orderProduct->id;
        $server = $this->getRequest($url)->json();
        
        $cpu = $configurableOptions['cpu'] ?? $params['cpu'];
        $cpu_pinning = $configurableOptions['cpu_pinning'] ?? $params['cpu_pinning'] ?? null;
        $io = $configurableOptions['io'] ?? $params['io'];
        $disk = $configurableOptions['disk'] ?? $params['disk'];
        $swap = $configurableOptions['swap'] ?? $params['swap'];
        $memory = $configurableOptions['memory'] ?? $params['memory'];
        $allocations = $configurableOptions['allocation'] ?? $params['allocation'];
        $databases = $configurableOptions['databases'] ?? $params['databases'];
        $backups = $configurableOptions['backups'] ?? $params['backups'];

        $url = $this->config('host') . '/api/application/servers/' . $serverId . '/build';
        $json = [
            'allocation' => $server['attributes']['allocation'],
            'memory' => (int) $memory,
            'swap' => (int) $swap,
            'disk' => (int) $disk,
            'io' => (int) $io,
            'cpu' => (int) $cpu,
            'threads' => $cpu_pinning,
            'feature_limits' => [
                'databases' => $databases ? (int) $databases : null,
                'allocations' => (int) $allocations,
                'backups' => (int) $backups,
            ],
        ];

        $response = $this->patchRequest($url, $json);
        
        if(!$response->successful()) {
            ExtensionHelper::error('Pterodactyl', 'Failed to upgrade server for order ' . $orderProduct->id . ' with error ' . $response->body());
            return false;
        }

        $nest_id = $configurableOptions['nest_id'] ?? $params['nest'];
        $egg_id = $configurableOptions['egg'] ?? $params['egg'];
        $eggData = $this->getRequest($this->config('host') . '/api/application/nests/' . $nest_id . '/eggs/' . $egg_id . '?include=variables');
        if(!$eggData->successful()) {
            ExtensionHelper::error('Pterodactyl', 'Failed to get egg data for order ' . $orderProduct->id . ' with error ' . $eggData->body());
            return false;
        }

        $eggData = $eggData->json();
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
                $environment[$var] = $configurableOptions[$var];
            }
            if(isset($server['attributes']['container']['environment'][$var])) {
                $environment[$var] = $server['attributes']['container']['environment'][$var];
            } else {    
               $environment[$var] = $default;
            }
        }

        $json = [
            'environment' => $environment,
            'startup' => $server['attributes']['container']['startup_command'] ?? $configurableOptions['startup'] ?? $eggData['attributes']['startup'],
            'egg' => (int) $egg_id,
            'image' => $server['attributes']['container']['image'] ?? $eggData['attributes']['docker_image'],
            'skip_scripts' => false,
        ];

        $url = $this->config('host') . '/api/application/servers/' . $serverId . '/startup';

        $response = $this->patchRequest($url, $json);
        if(!$response->successful()) {
            ExtensionHelper::error('Pterodactyl', 'Failed to upgrade server for order ' . $orderProduct->id . ' with error ' . $response->body());
            return false;
        }

        return true;
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
