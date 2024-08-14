<?php

namespace App\Extensions\Servers\Convoy;

use App\Classes\Extensions\Server;
use App\Helpers\ExtensionHelper;
use App\Models\Product;
use Illuminate\Support\Facades\Http;

class Convoy extends Server
{

    private $hostname;
    private $apikey;

    public function __construct($extension)
    {
        parent::__construct($extension);
        $this->hostname = ExtensionHelper::getConfig('Convoy', 'hostname');
        $this->apikey = ExtensionHelper::getConfig('Convoy', 'api_key');
    }

    public function getMetadata()
    {
        return [
            'display_name' => 'Convoy',
            'version' => '1.4.0',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }

    public function getConfig()
    {
        return [
            [
                'name' => 'hostname',
                'type' => 'text',
                'friendlyName' => 'Hostname',
                'required' => true,
                'validation' => 'url:http,https'
            ],
            [
                'name' => 'api_key',
                'type' => 'text',
                'friendlyName' => 'API Key',
                'required' => true
            ]
        ];
    }

    public function getProductConfig($options)
    {
        return [
            [
                'name' => 'cpu',
                'type' => 'text',
                'friendlyName' => 'CPU Cores',
                'required' => true
            ],
            [
                'name' => 'ram',
                'type' => 'text',
                'friendlyName' => 'RAM (MiB)',
                'required' => true
            ],
            [
                'name' => 'disk',
                'type' => 'text',
                'friendlyName' => 'Disk (MiB)',
                'required' => true
            ],
            [
                'name' => 'bandwidth',
                'type' => 'text',
                'friendlyName' => 'Bandwidth (MiB) (leave empty for unlimited)',
                'required' => false
            ],
            [
                'name' => 'snapshot',
                'type' => 'text',
                'friendlyName' => 'Amount of snapshots',
                'required' => true
            ],
            [
                'name' => 'backups',
                'type' => 'text',
                'friendlyName' => 'Amount of backups (leave empty for unlimited)',
            ],
            [
                'name' => 'node',
                'type' => 'dropdown',
                'friendlyName' => 'Nodes',
                'required' => true,
                'options' => $this->getNodes()
            ],
            [
                'name' => 'auto_assign_ip',
                'type' => 'dropdown',
                'friendlyName' => 'Auto assign IP',
                'required' => true,
                'options' => [
                    [
                        'value' => 0,
                        'name' => 'No IP'
                    ],
                    [
                        'value' => 4,
                        'name' => 'IPv4'
                    ],
                    [
                        'value' => 6,
                        'name' => 'IPv6'
                    ],
                    [
                        'value' => 10,
                        'name' => 'IPv4 & IPv6'
                    ]
                ]
            ],
        ];
    }

    public function getUserConfig(Product $product)
    {
        $node = $product->settings()->where('name', 'node')->first();
        return [
            [
                'name' => 'hostname',
                'type' => 'text',
                'friendlyName' => 'Hostname',
                'required' => true,
                'validation' => 'min:4|regex:/^[a-zA-Z0-9.-]+$/'
            ],
            [
                'name' => 'os',
                'type' => 'dropdown',
                'friendlyName' => 'Operating System',
                'required' => true,
                'options' => $this->getOS($node->value)
            ],
            [
                'name' => 'password',
                'type' => 'password',
                'friendlyName' => 'Password',
                'required' => true,
                'validation' => 'min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/'
            ],
        ];
    }

    public function createServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        $node = $configurableOptions['node'] ?? $params['node'];
        $os = $params['config']['os'];
        $hostname = $params['config']['hostname'];
        $password = $params['config']['password'];
        $cpu = $configurableOptions['cpu'] ?? $params['cpu'];
        $ram = $configurableOptions['ram'] ?? $params['ram'];
        $disk = $configurableOptions['disk'] ?? $params['disk'];
        $bandwidth = $configurableOptions['bandwidth'] ?? $params['bandwidth'];
        $snapshot = $configurableOptions['snapshot'] ?? $params['snapshot'];
        $backups = $configurableOptions['backups'] ?? $params['backups'];
        if ($params['auto_assign_ip'] == 10) {
            $ipv6 = $this->request('get', 'nodes/' . $node . '/addresses?filter[server_id]&filter[type]=ipv6');
            $ipv4 = $this->request('get', 'nodes/' . $node . '/addresses?filter[server_id]&filter[type]=ipv4');
            $isIpv6 = $ipv6['meta']['pagination']['total'] != 0;
            $isIpv4 = $ipv4['meta']['pagination']['total'] != 0;
            if ($isIpv6 && $isIpv4) {
                $ip = [$ipv6['data'][0]['id'], $ipv4['data'][0]['id']];
            } else if ($isIpv6 && !$isIpv4) {
                $ip = [$ipv6['data'][0]['id']];
            } else if (!$isIpv6 && $isIpv4) {
                $ip = [$ipv4['data'][0]['id']];
            }
        } else if ($params['auto_assign_ip'] == 6) {
            $ipv6 = $this->request('get', 'nodes/' . $node . '/addresses?filter[server_id]&filter[type]=ipv6');
            $isIpv6 = $ipv6['meta']['pagination']['total'] != 0;
            if ($isIpv6) {
                $ip = [$ipv6['data'][0]['id']];
            }
        } else if ($params['auto_assign_ip'] == 4) {
            $ipv4 = $this->request('get', 'nodes/' . $node . '/addresses?filter[server_id]&filter[type]=ipv4');
            $isIpv4 = $ipv4['meta']['pagination']['total'] != 0;
            if ($isIpv4) {
                $ip = [$ipv4['data'][0]['id']];
            }
        } else {
            $ip = [];
        }
        $data = [
            'node_id' => (int) $node,
            'user_id' => $this->getUser($user, $params['config']['password']),
            'name' => $hostname . ' ' . $user->name,
            'hostname' => $hostname,
            'vmid' => null,
            'limits' => [
                'cpu' => (int) $cpu,
                'memory' => $ram * 1024 * 1024,
                'disk' => $disk * 1024 * 1024,
                'snapshots' => (int) $snapshot,
                'bandwidth' => !isset($bandwidth) ? null : (int) $bandwidth * 1024 * 1024,
                'backups' => !isset($backups) ? null : (int) $backups,
                'address_ids' => $ip,
            ],
            'account_password' => $password,
            'template_uuid' => $os,
            'should_create_server' => true,
            'start_on_completion' => false,
        ];

        $server = $this->request('post', 'servers', $data);

        if (!isset($server['data'])) {
            ExtensionHelper::error('Convoy', $server['message'] ?? 'Something went wrong');
        }

        ExtensionHelper::setOrderProductConfig('server_uuid', $server['data']['uuid'], $orderProduct->id);

        return $server['data']['id'];
    }

    public function suspendServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        $this->request('post', 'servers/' . $params['config']['server_uuid'] . '/settings/suspend');
    }

    public function unsuspendServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        $this->request('post', 'servers/' . $params['config']['server_uuid'] . '/settings/unsuspend');
    }

    public function terminateServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        $this->request('delete', 'servers/' . $params['config']['server_uuid']);
    }

    private function getUser($user, $password)
    {
        $userr = $this->request('get', 'users', [
            'filter[email]' => $user->email
        ]);
        if (count($userr['data']) == 0) {
            $userr = $this->request('post', 'users', [
                'email' => $user->email,
                'password' => $password,
                'password_confirmation' => $password,
                'name' => $user->name,
                'root_admin' => false
            ]);

            return $userr['data']['id'];
        }
        return $userr['data'][0]['id'];
    }

    private function getOS($node)
    {
        $os = $this->request('get', 'nodes/' . $node . '/template-groups');
        $options = [];
        foreach ($os['data'] as $os) {
            foreach ($os['templates'] as $template) {
                foreach ($template as $template1) {
                    $options[] = [
                        'value' => $template1['uuid'],
                        'name' => $template1['name']
                    ];
                }
            }
        }
        return $options;
    }

    private function getNodes()
    {
        $nodes = $this->request('get', 'nodes');
        $options = [];
        foreach ($nodes['data'] as $node) {
            $options[] = [
                'value' => $node['id'],
                'name' => $node['name']
            ];
        }
        return $options;
    }

    private function request($method, $url, $data = [])
    {
        if (!empty($data)) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apikey,
            ])->acceptJson()->$method($this->hostname . '/api/application/' . $url, $data);
        } else {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apikey,
            ])->acceptJson()->$method($this->hostname . '/api/application/' . $url);
        }

        return $response->json();
    }

    public function getLink($user, $params, $order, $orderProduct): bool|string
    {
        $server = $this->request('post', 'users/' . $this->getUser($user, $params['config']['password']) . '/generate-sso-token');
        return $this->hostname . '/authenticate?token=' . $server['data']['token'];
    }
}
