<?php

namespace App\Extensions\Servers\Pterodactyl;

use App\Classes\Extension\Server;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

/**
 * Class Pterodactyl
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
            $this->request('/api/application/servers', 'GET');
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

        if (!$response->successful()) {
            throw new \Exception($response->json()['errors'][0]['detail']);
        }

        return $response->json() ?? [];
    }

    public function getProductConfig($values = []): array
    {
        $nodes = $this->request('/api/application/nodes');
        $nodeList = [];
        foreach ($nodes['data'] as $node) {
            $nodeList[$node['attributes']['id']] = $node['attributes']['name'];
        }

        $location = $this->request('/api/application/locations');
        $locationList = [];
        foreach ($location['data'] as $location) {
            $locationList[$location['attributes']['id']] = $location['attributes']['short'];
        }

        $nests = $this->request('/api/application/nests');
        $nestList = [];
        foreach ($nests['data'] as $nest) {
            $nestList[$nest['attributes']['id']] = $nest['attributes']['name'];
        }

        $eggList = [];
        if (isset($values['nest_id'])) {
            $eggs = $this->request('/api/application/nests/' . $values['nest_id'] . '/eggs');
            foreach ($eggs['data'] as $egg) {
                $eggList[$egg['attributes']['id']] = $egg['attributes']['name'];
            }
        }

        return [
            [
                'name' => 'location_id',
                'label' => 'Location',
                'type' => 'select',
                'description' => 'Location where the server will be installed',
                'options' => $locationList,
                'required' => true,
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
            [
                'name' => 'memory',
                'label' => 'Memory',
                'type' => 'number',
                'suffix' => 'MiB',
                'required' => true,
                'validation' => 'numeric',
            ],
            [
                'name' => 'swap',
                'label' => 'Swap',
                'type' => 'number',
                'min_value' => 0,
                'suffix' => 'MiB',
                'required' => true,
            ],
            [
                'name' => 'disk',
                'label' => 'Disk',
                'type' => 'number',
                'suffix' => 'MiB',
                'required' => true,
            ],
            [
                'name' => 'io',
                'label' => 'IO',
                'type' => 'number',
                'required' => true,
                'default' => 500,
                'min_value' => 10,
                'max_value' => 1000,
                'description' => 'The IO performance of the server',
            ],
            [
                'name' => 'cpu',
                'label' => 'CPU Limit',
                'type' => 'number',
                'required' => true,
                'min_value' => 0,
                'suffix' => '%',
            ],
            [
                'name' => 'cpu_pinning',
                'label' => 'CPU Pinning',
                'type' => 'text',
            ],
            [
                'name' => 'databases',
                'label' => 'Databases',
                'type' => 'number',
                'required' => true,
                'min_value' => 0,
            ],
            [
                'name' => 'backups',
                'label' => 'Backups',
                'type' => 'number',
                'required' => true,
                'min_value' => 0,
            ],
            [
                'name' => 'allocations',
                'label' => 'Allocations',
                'type' => 'number',
                'required' => true,
                'min_value' => 0,
            ],
            [
                'name' => 'skip_scripts',
                'label' => 'Skip Scripts',
                'type' => 'checkbox',
            ],
            [
                'name' => 'port_array',
                'label' => 'Port Array',
                'type' => 'text',
                'description' => new HtmlString('Used to assign ports to egg variables.'),
                'hint' => new HtmlString('<a href="https://docs.paymenter.org/docs/servers/pterodactyl" target="_blank">Documentation</a>'),
            ],
        ];
    }

    public function createServer(OrderProduct $orderProduct, $settings, $properties)
    {
        if ($this->serverExists($orderProduct->id)) {
            throw new \Exception('Server already exists');
        }
        // Smash the properties into the settings
        $settings = array_merge($settings, $properties);

        $eggData = $this->request('/api/application/nests/' . $settings['nest_id'] . '/eggs/' . $settings['egg_id'], data: ['include' => 'variables']);
        if (!isset($eggData['attributes'])) {
            throw new \Exception('Could not fetch egg data');
        }
        $environment = [];
        foreach ($eggData['attributes']['relationships']['variables']['data'] as $variable) {
            $environment[$variable['attributes']['env_variable']] = $settings[$variable['attributes']['env_variable']] ?? $variable['attributes']['default_value'];
        }

        [$environment, $allocations, $default] = $this->portArray($settings, $environment);

        if ($settings['node'] && !$default) {
            // Grab a random port from the node
            $node = $this->request('/api/application/nodes/' . $settings['node'] . '/allocations', data: ['server_id' => null, 'per_page' => 1]);
            if (count($node['data']) == 0) {
                throw new \Exception('Could not find a port to assign');
            }
            $default = $node['data'][0]['attributes']['id'];
        }

        $orderUser = $orderProduct->order->user;

        $returnData = [];

        // Create user
        $user = $this->request('/api/application/users', 'get', ['filter' => ['email' => $orderUser->email]])['data'][0]['attributes']['id'] ?? null;

        if (!$user) {
            $password = Str::password(12);

            $user = $this->request('/api/application/users', 'post', [
                'email' => $orderUser->email,
                'username' => (preg_replace('/[^a-zA-Z0-9]/', '', strtolower($orderUser->username)) ?? Str::random(8)) . '_' . Str::random(4),
                'first_name' => $orderUser->first_name ?? '',
                'last_name' => $orderUser->last_name ?? '',
                'password' => $password,
            ])['attributes']['id'];
        }

        $data = [
            'name' => $orderProduct->product->name . '-' . $orderProduct->id,
            'user' => (int) $user,
            'egg' => $settings['egg_id'],
            'docker_image' => $eggData['attributes']['docker_image'],
            'startup' => $eggData['attributes']['startup'],
            'limits' => [
                'memory' => (int) $settings['memory'],
                'swap' => (int) $settings['swap'],
                'disk' => (int) $settings['disk'],
                'io' => (int) $settings['io'],
                'cpu' => (int) $settings['cpu'],
                'allocations' => $settings['allocations'],
                'threads' => $settings['cpu_pinning'] ?? null,
            ],
            'feature_limits' => [
                'databases' => $settings['databases'],
                'allocations' => $settings['allocations'],
                'backups' => $settings['backups'],
            ],
            'environment' => $environment,
            'allocation' => [
                'default' => (int) $default ?? null,
                'additional' => $allocations,
            ],
            'skip_scripts' => $settings['skip_scripts'] ?? false,
            'external_id' => (string) $orderProduct->id,
        ];

        if (!$allocations && !$default) {
            $json['deploy'] = [
                'locations' => [(int) $settings['location_id']],
                'dedicated_ip' => false,
            ];
        }

        $server = $this->request('/api/application/servers', 'post', $data);

        // Add link to return data as well as the server id
        $returnData['server'] = $server['attributes']['id'];
        $returnData['link'] = $this->config('host') . '/server/' . $server['attributes']['id'];

        return $returnData;
    }

    private function portArray($settings, $environment)
    {
        // Example: {"SERVER_PORT": 7777, "NONE": [7778, 7779], "QUERY_PORT": 2701, "RCON_PORT": 27020}
        if (!isset($settings['port_array'])) {
            return [$environment, [], null];
        }
        try {
            $port_array = json_decode($settings['port_array'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('JSON decode error: ' . json_last_error_msg());
            }
        } catch (\Exception $e) {
            throw new \Exception('Invalid JSON in port array');
        }

        if (!is_array($port_array)) {
            throw new \Exception('Port array must be an array');
        }
        $node = $settings['node']; // Node ID
        // If there is no node selected, fetch the first node with available resources
        $page = 1;
        while (!$node) {
            $nodes = $this->request('/api/application/nodes?page=' . $page);
            foreach ($nodes['data'] as $reqNode) {
                if ($reqNode['attributes']['maintenance_mode']) {
                    continue;
                }
                if (
                    (($reqNode['attributes']['memory'] / 100 * ($reqNode['attributes']['memory_overallocate'] + 100)) > $reqNode['attributes']['allocated_resources']['memory'] &&
                        ($reqNode['attributes']['disk'] / 100 * ($reqNode['attributes']['disk_overallocate'] + 100)) > $reqNode['attributes']['allocated_resources']['disk']) ||
                    ($reqNode['attributes']['memory_overallocate'] == -1 && $reqNode['attributes']['disk_overallocate'] == -1)
                ) {
                    $node = $reqNode['attributes']['id'];
                    break;
                }
            }
            if ($nodes['meta']['pagination']['current_page'] == $nodes['meta']['pagination']['total_pages']) {
                throw new \Exception('Could not find a node with available resources');
            }
            $page++;
        }

        $availablePorts = [];
        $ports = $this->request('/api/application/nodes/' . $node . '/allocations', data: ['filter' => ['server_id' => false], 'per_page' => 100]);

        while ($ports['meta']['pagination']['current_page'] != $ports['meta']['pagination']['total_pages']) {
            foreach ($ports['data'] as $port) {
                $availablePorts[] = [
                    'port' => $port['attributes']['port'],
                    'id' => $port['attributes']['id'],
                ];
            }
            $ports = $this->request('/api/application/nodes/' . $node . '/allocations', data: ['filter' => ['server_id' => false], 'per_page' => 100, 'page' => $ports['meta']['pagination']['current_page'] + 1]);
        }

        $availablePorts = collect($availablePorts);

        $allocations = [];
        foreach ($port_array as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $port) {
                    $allocation = $availablePorts->where('port', $port)->first();
                    if (!$allocation) {
                        // try to assign a higher port, if that fails try a random port
                        $allocation = $availablePorts->where('port', '>', $port)->first();
                        if (!$allocation) {
                            $allocation = $availablePorts->random();
                        }
                        if (!$allocation) {
                            throw new \Exception('Could not find a port to assign');
                        }
                    }
                    $allocations[$key][] = $allocation;

                    // Remove the port from the available ports
                    $availablePorts = $availablePorts->reject(function ($port) use ($allocation) {
                        return $port['id'] == $allocation['id'];
                    });
                }
            } else {
                $allocation = $availablePorts->where('port', $value)->first();
                if (!$allocation) {
                    // try to assign a higher port, if that fails try a random port
                    $allocation = $availablePorts->where('port', '>', $value)->first();
                    if (!$allocation) {
                        $allocation = $availablePorts->random();
                    }
                    if (!$allocation) {
                        throw new \Exception('Could not find a port to assign');
                    }
                }
                $allocations[$key] = $allocation;

                // Remove the port from the available ports
                $availablePorts = $availablePorts->reject(function ($port) use ($allocation) {
                    return $port['id'] == $allocation['id'];
                });
            }
        }

        // Assign the allocations to the environment
        foreach ($allocations as $key => $value) {
            if (isset($environment[$key])) {
                $environment[$key] = $value['port'];
            }
        }

        $default = $allocations['SERVER_PORT']['id'] ?? null;

        $allocationIds = [];

        // Set allocations to a array with only the ids
        foreach ($allocations as $key => $value) {
            if ($key == 'SERVER_PORT') {
                continue;
            }
            if (is_array($value) && isset($value[0])) {
                foreach ($value as $v) {
                    $allocationIds[] = $v['id'];
                }
            } else {
                $allocationIds[] = $value['id'];
            }
        }

        return [
            $environment,
            $allocationIds,
            $default,
        ];
    }

    private function serverExists($id)
    {
        try {
            $response = $this->request('/api/application/servers/external/' . $id);
        } catch (\Exception $e) {
            return false;
        }

        return $response['attributes']['id'] ?? false;
    }

    public function suspendServer(OrderProduct $orderProduct, $settings, $properties)
    {
        $server = $this->serverExists($orderProduct->id);
        if (!$server) {
            throw new \Exception('Server not found');
        }

        $this->request('/api/application/servers/' . $server . '/suspend', 'post');

        return true;
    }

    public function unsuspendServer(OrderProduct $orderProduct, $settings, $properties)
    {
        $server = $this->serverExists($orderProduct->id);
        if (!$server) {
            throw new \Exception('Server not found');
        }

        $this->request('/api/application/servers/' . $server . '/unsuspend', 'post');

        return true;
    }

    public function terminateServer(OrderProduct $orderProduct, $settings, $properties)
    {
        $server = $this->serverExists($orderProduct->id);
        if (!$server) {
            throw new \Exception('Server not found');
        }

        $this->request('/api/application/servers/' . $server, 'delete');

        return true;
    }
}
