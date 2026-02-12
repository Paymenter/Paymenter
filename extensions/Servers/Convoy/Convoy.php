<?php

namespace Paymenter\Extensions\Servers\Convoy;

use App\Classes\Extension\Server;
use App\Models\Product;
use App\Models\Service;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Convoy extends Server
{
    public function request($url, $method = 'get', $data = []): array
    {
        // Trim any leading slashes from the base url and add the path URL to it
        $req_url = rtrim($this->config('host'), '/') . '/api/application/' . $url;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('api_key'),
            'Accept' => 'application/json',
        ])->$method($req_url, $data);

        if (!$response->successful()) {
            throw new Exception($response->json()['message']);
        }

        return $response->json() ?? [];
    }

    /**
     * Get all the configuration for the extension
     *
     * @param  array  $values
     */
    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'host',
                'type' => 'text',
                'label' => 'Hostname',
                'required' => true,
                'validation' => 'url:http,https',
            ],
            [
                'name' => 'api_key',
                'type' => 'text',
                'label' => 'API Key',
                'required' => true,
            ],
        ];
    }

    /**
     * Get product config
     *
     * @param  array  $values
     */
    public function getProductConfig($values = []): array
    {
        $nodes = $this->request('nodes');
        $options = [];
        foreach ($nodes['data'] as $node) {
            $options[$node['id']] = $node['name'];
        }

        return [
            [
                'name' => 'cpu',
                'type' => 'text',
                'label' => 'CPU Cores',
                'required' => true,
            ],
            [
                'name' => 'ram',
                'type' => 'text',
                'label' => 'RAM (MiB)',
                'required' => true,
            ],
            [
                'name' => 'disk',
                'type' => 'text',
                'label' => 'Disk (MiB)',
                'required' => true,
            ],
            [
                'name' => 'bandwidth',
                'type' => 'text',
                'label' => 'Bandwidth (MiB)',
                'required' => false,
            ],
            [
                'name' => 'snapshot',
                'type' => 'text',
                'label' => 'Amount of snapshots',
                'required' => true,
            ],
            [
                'name' => 'backups',
                'type' => 'text',
                'label' => 'Amount of backups',
                'required' => true,
            ],
            [
                'name' => 'node',
                'type' => 'select',
                'label' => 'Nodes',
                'required' => true,
                'options' => $options,
            ],
            [
                'name' => 'auto_assign_ip',
                'type' => 'checkbox',
                'label' => 'Auto assign IP from pool',
                'required' => false,
            ],
            [
                'name' => 'ipv4',
                'type' => 'number',
                'label' => 'Amount of IPv4 addresses',
                'required' => false,
            ],
            [
                'name' => 'ipv6',
                'type' => 'number',
                'label' => 'Amount of IPv6 addresses',
                'required' => false,
            ],
            [
                'name' => 'start_on_create',
                'type' => 'checkbox',
                'label' => 'Start Server After Completing Installation',
                'required' => false,
            ],
        ];
    }

    public function getCheckoutConfig(Product $product): array
    {
        $node = $product->settings()->where('key', 'node')->first()->value;

        $os = $this->request('nodes/' . $node . '/template-groups');
        $options = [];
        foreach ($os['data'] as $os) {
            foreach ($os['templates'] as $template) {
                foreach ($template as $template1) {
                    $options[$template1['uuid']] = $template1['name'];
                }
            }
        }

        return [
            [
                'name' => 'os',
                'type' => 'select',
                'label' => 'Operating System',
                'required' => true,
                'options' => $options,
            ],
            [
                'name' => 'hostname',
                'type' => 'text',
                'label' => 'Hostname',
                'placeholder' => 'server.example.com',
                'required' => true,
                'validation' => 'required|string|max:40',
            ],
        ];
    }

    /**
     * Check if currenct configuration is valid
     */
    public function testConfig(): bool|string
    {
        try {
            $this->request('servers');

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    // Convoy is reallyy strict (The account password must contain 8 - 50 characters, 1 uppercase, 1 lowercase, 1 number and 1 special character.)
    private function createPassword()
    {
        $password = Str::password();
        while (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#\$%\^&\*]).{8,50}$/', $password)) {
            $password = Str::password();
        }

        return $password;
    }

    public function getOrCreateUser($user)
    {
        $users = $this->request('users', data: ['filter[email]' => $user->email]);

        if (count($users['data']) > 0) {
            return [
                'created' => false,
                'id' => $users['data'][0]['id'],
            ];
        }

        $password = $this->createPassword();
        $user = $this->request('users', 'post', [
            'email' => $user->email,
            'name' => $user->name,
            'password' => $password,
            'root_admin' => false,
        ]);

        return [
            'created' => true,
            'id' => $user['data']['id'],
            'password' => $password,
        ];
    }

    /**
     * Create a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function createServer(Service $service, $settings, $properties)
    {
        $node = $properties['node'] ?? $settings['node'];
        $os = $properties['os'];
        $hostname = $properties['hostname'];
        $password = $properties['password'] ?? $this->createPassword();
        $cpu = $properties['cpu'] ?? $settings['cpu'];
        $ram = $properties['ram'] ?? $settings['ram'];
        $disk = $properties['disk'] ?? $settings['disk'];
        $bandwidth = $properties['bandwidth'] ?? $settings['bandwidth'];
        $snapshot = $properties['snapshot'] ?? $settings['snapshot'];
        $backups = $properties['backups'] ?? $settings['backups'];
        $ipv4 = $properties['ipv4'] ?? $settings['ipv4'];
        $ipv6 = $properties['ipv6'] ?? $settings['ipv6'];

        $ips = [];
        if ($ipv4 > 0) {
            $ip = $this->request('nodes/' . $node . '/addresses', data: ['filter[server_id]' => '', 'filter[type]' => 'ipv4', 'per_page' => $ipv4]);
            $ips = array_merge($ips, array_column($ip['data'], 'id'));
        }
        if ($ipv6 > 0) {
            $ip = $this->request('nodes/' . $node . '/addresses', data: ['filter[server_id]' => '', 'filter[type]' => 'ipv6', 'per_page' => $ipv6]);
            $ips = array_merge($ips, array_column($ip['data'], 'id'));
        }

        $user = $this->getOrCreateUser($service->user);

        $data = [
            'node_id' => (int) $node,
            'user_id' => $user['id'],
            'name' => Str::substr($hostname . ' ' . $service->user->name, 0, 40), // The server name must not be greater than 40 characters
            'hostname' => $hostname,
            'vmid' => null,
            'limits' => [
                'cpu' => (int) $cpu,
                'memory' => $ram * 1024 * 1024,
                'disk' => $disk * 1024 * 1024,
                'snapshots' => (int) $snapshot,
                'bandwidth' => (int) $bandwidth == 0 ? null : (int) $bandwidth * 1024 * 1024,
                'backups' => (int) $backups,
                'address_ids' => $ips,
            ],
            'account_password' => $password,
            'template_uuid' => $os,
            'should_create_server' => true,
            'start_on_completion' => isset($properties['start_on_create']) ? (bool) $properties['start_on_create'] : (bool) $settings['start_on_create'],
        ];

        $server = $this->request('servers', 'post', $data);

        if (!isset($server['data'])) {
            throw new Exception('Failed to create server');
        }

        $service->properties()->updateOrCreate([
            'key' => 'server_uuid',
        ], [
            'name' => 'Convoy Server UUID',
            'value' => $server['data']['uuid'],
        ]);

        return [
            'user' => $user,
            'password' => $password,
            'server' => $server['data'],
        ];
    }

    public function upgradeServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_uuid'])) {
            throw new Exception('Server does not exist');
        }

        $currentData = $this->request('servers/' . $properties['server_uuid']);

        $data = [
            'address_ids' => [],
            'snapshot_limit' => (int) ($properties['snapshot'] ?? $settings['snapshot']),
            'backup_limit' => (int) ($properties['backups'] ?? $settings['backups']),
            'bandwidth_limit' => (int) ($properties['bandwidth'] ?? $settings['bandwidth']) * 1024 * 1024,
            'cpu' => (int) ($properties['cpu'] ?? $settings['cpu']),
            'memory' => (int) ($properties['ram'] ?? $settings['ram']) * 1024 * 1024,
            'disk' => (int) ($properties['disk'] ?? $settings['disk']) * 1024 * 1024,
        ];

        $limitIpv4 = (int) ($properties['ipv4'] ?? $settings['ipv4']);
        $limitIpv6 = (int) ($properties['ipv6'] ?? $settings['ipv6']);
        // Check if IPv4 has increased
        if ($limitIpv4 && $limitIpv4 > count($currentData['data']['limits']['addresses']['ipv4'])) {
            $ip = $this->request('nodes/' . $currentData['data']['node_id'] . '/addresses', data: ['filter[server_id]' => '', 'filter[type]' => 'ipv4', 'per_page' => $limitIpv4 - count($currentData['data']['limits']['addresses']['ipv4'])]);
            $data['address_ids'] = array_merge(array_column($currentData['data']['limits']['addresses']['ipv4'], 'id'), array_column($ip['data'], 'id'));
        } else {
            $data['address_ids'] = array_column($currentData['data']['limits']['addresses']['ipv4'], 'id');
        }
        // Check if IPv6 has increased
        if ($limitIpv6 && $limitIpv6 > count($currentData['data']['limits']['addresses']['ipv6'])) {
            $ip = $this->request('nodes/' . $currentData['data']['node_id'] . '/addresses', data: ['filter[server_id]' => '', 'filter[type]' => 'ipv6', 'per_page' => $limitIpv6 - count($currentData['data']['limits']['addresses']['ipv6'])]);
            $data['address_ids'] = array_merge($data['address_ids'], array_column($ip['data'], 'id'));
        } else {
            $data['address_ids'] = array_merge($data['address_ids'], array_column($currentData['data']['limits']['addresses']['ipv6'], 'id'));
        }
        $data['address_ids'] = array_values(array_unique($data['address_ids']));

        // Update server
        $server = $this->request('servers/' . $properties['server_uuid'] . '/settings/build', 'patch', $data);
        if (!isset($server['data'])) {
            throw new Exception('Failed to update server');
        }

        return [
            'server' => $server['data'],
        ];
    }

    /**
     * Suspend a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function suspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_uuid'])) {
            throw new Exception('Server does not exist');
        }

        $this->request('servers/' . $properties['server_uuid'] . '/settings/suspend', 'post');
    }

    /**
     * Unsuspend a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function unsuspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_uuid'])) {
            throw new Exception('Server does not exist');
        }

        $this->request('servers/' . $properties['server_uuid'] . '/settings/unsuspend', 'post');
    }

    /**
     * Terminate a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function terminateServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_uuid'])) {
            throw new Exception('Server does not exist');
        }

        $this->request('servers/' . $properties['server_uuid'], 'delete');

        // Remove the server_uuid property
        $service->properties()->where('key', 'server_uuid')->delete();
    }

    public function getActions(Service $service): array
    {
        return [
            [
                'type' => 'button',
                'label' => 'Go to Server',
                'function' => 'ssoLink',
            ],
        ];
    }

    public function ssoLink(Service $service): string
    {
        $data = $this->request('users/' . $this->getOrCreateUser($service->user)['id'] . '/generate-sso-token', 'post');

        return rtrim($this->config('host'), '/') . '/authenticate?token=' . $data['data']['token'];
    }
}
