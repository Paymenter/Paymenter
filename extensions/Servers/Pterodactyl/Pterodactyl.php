<?php

namespace Paymenter\Extensions\Servers\Pterodactyl;

use App\Classes\Extension\Server;
use App\Models\Service;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

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
                'encrypted' => true,
            ],
        ];
    }

    public function testConfig(): bool|string
    {
        try {
            $this->request('/api/application/servers', 'GET');
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function request($url, $method = 'get', $data = []): array
    {
        // Trim any leading slashes from the base url and add the path URL to it
        $req_url = rtrim($this->config('host'), '/') . $url;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('api_key'),
            'Accept' => 'application/json',
        ])->$method($req_url, $data);

        if (!$response->successful()) {
            throw new Exception($response->json()['errors'][0]['detail']);
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
        if (isset($values['nest_id']) && $values['nest_id'] !== '') {
            $eggs = $this->request('/api/application/nests/' . $values['nest_id'] . '/eggs');
            foreach ($eggs['data'] as $egg) {
                $eggList[$egg['attributes']['id']] = $egg['attributes']['name'];
            }
        }

        $using_port_array = isset($values['port_array']) && $values['port_array'] !== '';

        return [
            [
                'name' => 'location_ids',
                'label' => 'Location(s)',
                'type' => 'select',
                'description' => 'Location(s) where the server will be installed',
                'options' => $locationList,
                'multiple' => true,
                'database_type' => 'array',
                'required' => false,
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
                'min_value' => 0,
                'description' => 'Set to 0 for unlimited',
            ],
            [
                'name' => 'swap',
                'label' => 'Swap',
                'type' => 'number',
                'min_value' => -1,
                'suffix' => 'MiB',
                'required' => true,
                'description' => 'Set to -1 for unlimited, or to 0 to disable swap',
            ],
            [
                'name' => 'disk',
                'label' => 'Disk',
                'type' => 'number',
                'suffix' => 'MiB',
                'required' => true,
                'min_value' => 0,
                'description' => 'Set to 0 for unlimited',
            ],
            [
                'name' => 'io',
                'label' => 'IO Weight',
                'type' => 'number',
                'required' => true,
                'default' => 500,
                'min_value' => 10,
                'max_value' => 1000,
                'description' => 'The IO Weight is the priority given to this server for disk access.',
                'hint' => new HtmlString('<a href="https://docs.docker.com/engine/reference/run/#block-io-bandwidth-blkio-constraint" target="_blank">Documentation</a>'),
            ],
            [
                'name' => 'cpu',
                'label' => 'CPU Limit',
                'type' => 'number',
                'required' => true,
                'min_value' => 0,
                'suffix' => '%',
                'description' => 'Set to 0 for unlimited',
            ],
            [
                'name' => 'cpu_pinning',
                'label' => 'CPU Pinning',
                'type' => 'text',
                'description' => 'Leave empty for no pinning. Used to specify what threads should be used. Example: 0,2-4,5,6',
                'validation' => 'regex:/^[0-9]+(?:-[0-9]+)?(?:,[0-9]+(?:-[0-9]+)?)*$/',
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
                'name' => 'additional_allocations',
                'label' => 'Additional Allocations',
                'type' => 'number',
                'required' => true,
                'min_value' => 0,
            ],
            [
                'name' => 'port_array',
                'label' => 'Port Array',
                'type' => 'text',
                'description' => 'Used to assign ports to egg variables.',
                'hint' => new HtmlString('<a href="https://paymenter.org/docs/extensions/pterodactyl#port-array" target="_blank">Documentation</a>'),
                'live' => true,
                'validation' => 'json',
            ],
            [
                'name' => 'port_range',
                'label' => 'Port ranges',
                'type' => 'tags',
                'description' => '',
                'database_type' => 'array',
                'required' => false,
                'disabled' => $using_port_array,
            ],
            [
                'name' => 'skip_scripts',
                'label' => 'Skip Egg Install Script',
                'description' => 'If the selected Egg has an install script attached to it, the script will run during the install. If you would like to skip this step, check this box.',
                'type' => 'checkbox',
            ],
            [
                'name' => 'dedicated_ip',
                'label' => 'Dedicated IP',
                'description' => 'Assigns the server an allocation whose IP is not being used by any other server.',
                'type' => 'checkbox',
                'disabled' => $using_port_array,
            ],
            [
                'name' => 'start_on_completion',
                'label' => 'Start on completion',
                'description' => 'Start server automatically after installation.',
                'type' => 'checkbox',
            ],
            [
                'name' => 'oom_killer',
                'label' => 'Enable OOM Killer',
                'description' => 'Terminates the server if it breaches the memory limits. Enabling OOM killer may cause server processes to exit unexpectedly.',
                'type' => 'checkbox',
            ],
        ];
    }

    public function createServer(Service $service, $settings, $properties)
    {
        if ($this->getServer($service->id, failIfNotFound: false)) {
            throw new Exception('Server already exists');
        }
        // Smash the properties into the settings
        $settings = array_merge($settings, $properties);

        $eggData = $this->request('/api/application/nests/' . $settings['nest_id'] . '/eggs/' . $settings['egg_id'], data: ['include' => 'variables']);
        if (!isset($eggData['attributes'])) {
            throw new Exception('Could not fetch egg data');
        }
        $environment = [];
        foreach ($eggData['attributes']['relationships']['variables']['data'] as $variable) {
            $environment[$variable['attributes']['env_variable']] = $settings[$variable['attributes']['env_variable']] ?? $variable['attributes']['default_value'];
        }

        $orderUser = $service->user;
        // Get the user id if one already exists...
        $user = $this->request('/api/application/users', 'get', ['filter' => ['email' => $orderUser->email]])['data'][0]['attributes']['id'] ?? null;

        // Otherwise create a new user
        if (!$user) {
            $user = $this->request('/api/application/users', 'post', [
                'email' => $orderUser->email,
                'username' => (preg_replace('/[^a-zA-Z0-9]/', '', strtolower(Str::transliterate($orderUser->name))) ?? Str::random(8)) . '_' . Str::random(4),
                'first_name' => $orderUser->first_name ?? '',
                'last_name' => $orderUser->last_name ?? '',
            ])['attributes']['id'];

            $returnData['created_user'] = true;
        }

        if (isset($settings['location'])) {
            $settings['location_ids'] = [$settings['location']];
        }

        $deploymentData = $this->generateDeploymentData($settings, $environment);

        $serverCreationData = [
            'external_id' => (string) $service->id,
            'name' => isset($settings['servername']) ? $settings['servername'] : $service->product->name . ' #' . $service->id,
            'user' => (int) $user,
            'egg' => $settings['egg_id'],
            'docker_image' => isset($settings['docker_image']) ? $settings['docker_image'] : $eggData['attributes']['docker_image'],
            'startup' => $eggData['attributes']['startup'],
            'environment' => $deploymentData['environment'],
            'skip_scripts' => $settings['skip_scripts'] ?? false,
            'oom_disabled' => !($settings['oom_killer'] ?? false),
            'limits' => [
                'memory' => (int) $settings['memory'],
                'swap' => (int) $settings['swap'],
                'disk' => (int) $settings['disk'],
                'io' => (int) $settings['io'],
                'threads' => $settings['cpu_pinning'] ?? null,
                'cpu' => (int) $settings['cpu'],
            ],
            'feature_limits' => [
                'databases' => (int) $settings['databases'],
                'allocations' => $deploymentData['allocations_needed'] + (int) $settings['additional_allocations'],
                'backups' => (int) $settings['backups'],
            ],
            'start_on_completion' => $settings['start_on_completion'] ?? false,
        ];
        if ($deploymentData['auto_deploy']) {
            $serverCreationData['deploy'] = [
                'locations' => (array) $settings['location_ids'],
                'dedicated_ip' => $settings['dedicated_ip'] ?? false,
                'port_range' => $settings['port_range'] ?? [],
            ];
        } else {
            $serverCreationData['allocation'] = $deploymentData['allocation'];
        }

        $server = $this->request('/api/application/servers', 'post', $serverCreationData);

        return [
            'server' => $server['attributes']['id'],
            'link' => $this->config('host') . '/server/' . $server['attributes']['identifier'],
        ];
    }

    private function generateDeploymentData($settings, $environment)
    {
        if (!isset($settings['port_array']) || $settings['port_array'] === '') {
            if ($settings['node']) {
                // Only get one allocation from the node
                $nodes = $this->request('/api/application/nodes/deployable', 'get', [
                    'memory' => $settings['memory'],
                    'disk' => $settings['disk'],
                    'location_ids' => $settings['location_ids'] ?? [],
                    'include' => ['allocations'],
                ]);
                $nodes = collect($nodes['data']);
                $nodes_by_id = $nodes->mapWithKeys(fn ($node) => [$node['attributes']['id'] => $node['attributes']]);

                if (!$nodes_by_id->has($settings['node'])) {
                    throw new Exception('Node is not suitable for deployment.');
                }
                $node = $nodes_by_id->get($settings['node']);
                $availablePorts = collect($node['relationships']['allocations']['data']);
                $availablePorts = $availablePorts
                    ->filter(fn ($port) => !$port['attributes']['assigned'])
                    ->map(
                        fn ($port) => [
                            'port' => $port['attributes']['port'],
                            'id' => $port['attributes']['id'],
                        ]
                    );
                if ($availablePorts->isEmpty()) {
                    throw new Exception('No available allocations found on the selected node.');
                }
                $allocation = $availablePorts->first();
                $environment['SERVER_PORT'] = $allocation['port'];

                // Return the allocation id for the SERVER_PORT
                return [
                    'auto_deploy' => false,
                    'environment' => $environment,
                    'allocations_needed' => 1,
                    'allocation' => [
                        'default' => $allocation['id'],
                        'additional' => [],
                    ],
                ];
            }

            return [
                'auto_deploy' => true,
                'environment' => $environment,
                'allocations_needed' => 1,
            ];
        }

        try {
            // Example: {"SERVER_PORT": 7777, "NONE": [7778, 7779], "QUERY_PORT": 2701, "RCON_PORT": 27020}
            $port_array = json_decode($settings['port_array'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON decode error: ' . json_last_error_msg());
            }
        } catch (Exception $e) {
            throw new Exception('Invalid JSON in port array');
        }

        if (!is_array($port_array)) {
            throw new Exception('Port array must be an array');
        }

        $nodes = $this->request('/api/application/nodes/deployable', 'get', [
            'memory' => $settings['memory'],
            'disk' => $settings['disk'],
            'location_ids' => $settings['location_ids'] ?? [],
            'include' => ['allocations'],
        ]);
        $nodes = collect($nodes['data']);
        $nodes_by_id = $nodes->mapWithKeys(fn ($node) => [$node['attributes']['id'] => $node['attributes']]);

        if ($settings['node']) {
            // If the product's node id is not in the deployable nodes array, throw error.
            if (!$nodes_by_id->has($settings['node'])) {
                throw new Exception('Node is not suitable for deployment.');
            }

            $node = $nodes_by_id->get($settings['node']);
            $availablePorts = collect($node['relationships']['allocations']['data']);
            $availablePorts = $availablePorts
                ->filter(fn ($port) => !$port['attributes']['assigned'])
                ->map(
                    fn ($port) => [
                        'port' => $port['attributes']['port'],
                        'id' => $port['attributes']['id'],
                    ]
                );

            $free_allocations_needed = 0;
            foreach ($port_array as $key => $value) {
                $free_allocations_needed += is_array($value) ? count($value) : 1;
            }

            if (count($availablePorts) < $free_allocations_needed) {
                throw new Exception("Not enough allocations found for deployment. Found: {$availablePorts->count()}, Required: {$free_allocations_needed}");
            }
        } else {
            foreach ($nodes as $index => $node) {
                $availablePorts = collect($node['attributes']['relationships']['allocations']['data']);
                $availablePorts = $availablePorts
                    ->filter(fn ($port) => !$port['attributes']['assigned'])
                    ->map(
                        fn ($port) => [
                            'port' => $port['attributes']['port'],
                            'id' => $port['attributes']['id'],
                        ]
                    );

                $free_allocations_needed = 0;
                foreach ($port_array as $key => $value) {
                    $free_allocations_needed += is_array($value) ? count($value) : 1;
                }

                if (count($availablePorts) < $free_allocations_needed) {
                    // If this was last viable node, throw error
                    if ($index == $nodes->count() - 1) {
                        throw new Exception('No nodes with suitable allocations found for deployment');
                    }

                    // Else move onto next viable node
                    continue;
                }
                break;
            }
        }

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
                            throw new Exception('Could not find a port to assign');
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
                        throw new Exception('Could not find a port to assign');
                    }
                }
                $allocations[$key] = $allocation;

                // Remove the port from the available ports
                $availablePorts = $availablePorts->reject(function ($port) use ($allocation) {
                    return $port['id'] == $allocation['id'];
                });
            }
        }

        $allocationIds = [];

        foreach ($allocations as $key => $value) {
            // Assign the allocations to the environment
            if ($key !== 'NONE') {
                if (isset($environment[$key])) {
                    $environment[$key] = $value['port'];
                }
            }

            // Set allocations to a array with only the ids
            if ($key !== 'SERVER_PORT') {
                if (is_array($value) && isset($value[0])) {
                    foreach ($value as $v) {
                        $allocationIds[] = $v['id'];
                    }
                } else {
                    $allocationIds[] = $value['id'];
                }
            }
        }

        return [
            'auto_deploy' => false,
            'allocations_needed' => $free_allocations_needed,
            'environment' => $environment,
            'allocation' => [
                'default' => $allocations['SERVER_PORT']['id'],
                'additional' => $allocationIds,
            ],
        ];
    }

    private function getServer($id, $failIfNotFound = true, $raw = false)
    {
        try {
            $response = $this->request('/api/application/servers/external/' . $id);
        } catch (Exception $e) {
            if ($failIfNotFound) {
                throw new Exception('Server not found');
            } else {
                return false;
            }
        }
        if ($raw) {
            return $response;
        }

        return $response['attributes']['id'] ?? false;
    }

    public function suspendServer(Service $service, $settings, $properties)
    {
        $server = $this->getServer($service->id);

        $this->request('/api/application/servers/' . $server . '/suspend', 'post');

        return true;
    }

    public function unsuspendServer(Service $service, $settings, $properties)
    {
        $server = $this->getServer($service->id);

        $this->request('/api/application/servers/' . $server . '/unsuspend', 'post');

        return true;
    }

    public function terminateServer(Service $service, $settings, $properties)
    {
        $server = $this->getServer($service->id);

        $this->request('/api/application/servers/' . $server, 'delete');

        return true;
    }

    public function upgradeServer(Service $service, $settings, $properties)
    {
        $server = $this->getServer($service->id, raw: true);

        $settings = array_merge($settings, $properties);

        $updateServerData = [
            'allocation' => $server['attributes']['allocation'],
            'memory' => (int) $settings['memory'],
            'swap' => (int) $settings['swap'],
            'disk' => (int) $settings['disk'],
            'io' => (int) $settings['io'],
            'cpu' => (int) $settings['cpu'],
            'threads' => $settings['cpu_pinning'] ?? null,
            'feature_limits' => [
                'databases' => $settings['databases'],
                'allocations' => $settings['additional_allocations'],
                'backups' => $settings['backups'],
            ],
        ];

        $this->request('/api/application/servers/' . $server['attributes']['id'] . '/build', 'patch', $updateServerData);

        $eggData = $this->request('/api/application/nests/' . $settings['nest_id'] . '/eggs/' . $settings['egg_id'], data: ['include' => 'variables']);

        if (!isset($eggData['attributes'])) {
            throw new Exception('Could not fetch egg data');
        }

        $environment = [];

        foreach ($eggData['attributes']['relationships']['variables']['data'] as $variable) {
            // Check if variable has been set on server
            if (isset($server['attributes']['container']['environment'][$variable['attributes']['env_variable']])) {
                $environment[$variable['attributes']['env_variable']] = $server['attributes']['container']['environment'][$variable['attributes']['env_variable']];
            } else {
                $environment[$variable['attributes']['env_variable']] = $settings[$variable['attributes']['env_variable']] ?? $variable['attributes']['default_value'];
            }
        }

        $updateServerData = [
            'environment' => $environment,
            'skip_scripts' => $settings['skip_scripts'] ?? false,
            'oom_disabled' => !($settings['oom_killer'] ?? false),
            'egg' => $settings['egg_id'],
            'image' => $server['attributes']['container']['image'] ?? $eggData['attributes']['docker_image'],
            'startup' => $server['attributes']['container']['startup_command'] ?? $settings['startup'] ?? $eggData['attributes']['startup'],
        ];

        $this->request('/api/application/servers/' . $server['attributes']['id'] . '/startup', 'patch', $updateServerData);

        return true;
    }

    public function getActions(Service $service)
    {
        $server = $this->getServer($service->id, raw: true);

        return [
            [
                'type' => 'button',
                'label' => 'Go to server',
                'url' => $this->config('host') . '/server/' . $server['attributes']['identifier'],
            ],
        ];
    }

    public function migrateOption(string $key, ?string $value)
    {
        return match ($key) {
            'egg' => ['key' => 'egg_id', 'value' => $value],
            'nest' => ['key' => 'nest_id', 'value' => $value],
            'allocation' => ['key' => 'additional_allocations', 'value' => $value],
            'location' => ['key' => 'location_ids', 'value' => json_encode([$value]), 'type' => 'array'],
            default => ['key' => $key, 'value' => $value]
        };
    }
}
