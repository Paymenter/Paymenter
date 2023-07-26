<?php

namespace App\Extensions\Servers\Virtualizor;

use App\Classes\Extensions\Server;
use App\Models\Product;
use App\Helpers\ExtensionHelper;

use App\Extensions\Servers\Virtualizor\Virtualizor_Admin_API;

class Virtualizor extends Server
{
    public function getConfig()
    {
        return [
            [
                'name' => 'key',
                'type' => 'text',
                'friendlyName' => 'API Key',
                'required' => true,
            ],
            [
                'name' => 'pass',
                'type' => 'text',
                'friendlyName' => 'API Password',
                'required' => true,
            ],
            [
                'name' => 'ip',
                'type' => 'text',
                'friendlyName' => 'IP Address',
                'required' => true,
            ],
            [
                'name' => 'port',
                'type' => 'text',
                'friendlyName' => 'Port',
                'required' => true,
            ],
        ];
    }

    public function getUserConfig(Product $product)
    {
        $key = ExtensionHelper::getConfig('virtualizor', 'key');
        $pass = ExtensionHelper::getConfig('virtualizor', 'pass');
        $ip = ExtensionHelper::getConfig('virtualizor', 'ip');
        $port = ExtensionHelper::getConfig('virtualizor', 'port');
        $admin = new Virtualizor_Admin_API();
        $admin->Virtualizor_Admin_API($ip, $key, $pass, $port);
        // Get os list
        $os = $admin->ostemplates();
        $allos = [];
        foreach ($os['oslist'][$product->settings()->get()->where('name', 'virt')->first()->value] as $osid => $osname) {
            foreach ($osname as $osid => $osname) {
                $allos[] = [
                    'name' => $osname['name'],
                    'value' => $osid,
                ];
            }
        }

        return [
            [
                'name' => 'hostname',
                'type' => 'text',
                'friendlyName' => 'Hostname',
                'required' => true,
            ],
            [
                'name' => 'password',
                'type' => 'text',
                'friendlyName' => 'Password',
                'required' => true,
            ],
            [
                'name' => 'os',
                'type' => 'dropdown',
                'friendlyName' => 'Operating System',
                'required' => true,
                'options' => $allos,
            ],
        ];
    }

    public function getProductConfig($options)
    {
        $key = ExtensionHelper::getConfig('virtualizor', 'key');
        $pass = ExtensionHelper::getConfig('virtualizor', 'pass');
        $ip = ExtensionHelper::getConfig('virtualizor', 'ip');
        $port = ExtensionHelper::getConfig('virtualizor', 'port');
        $admin = new Virtualizor_Admin_API();
        $admin->Virtualizor_Admin_API($ip, $key, $pass, $port);

        // Get Plan list
        $plans = $admin->plans();
        $allplans = [];
        foreach ($plans['plans'] as $plan) {
            $allplans[] = [
                'name' => $plan['plan_name'],
                'value' => $plan['plan_name'],
            ];
        }

        return [
            [
                'name' => 'virt',
                'friendlyName' => 'Virtualizon Type',
                'type' => 'dropdown',
                'required' => true,
                'options' => [
                    [
                        'name' => 'OpenVZ',
                        'value' => 'openvz',
                    ],
                    [
                        'name' => 'Xen',
                        'value' => 'xen',
                    ],
                    [
                        'name' => 'KVM',
                        'value' => 'kvm',
                    ],
                ],
            ],
            [
                'name' => 'planname',
                'friendlyName' => 'Plan Name',
                'type' => 'dropdown',
                'required' => true,
                'options' => $allplans,
            ],
        ];
    }

    public function createServer($user, $params, $order, $product, $configurableOptions)
    {
        $config = $params['config'];
        $key = ExtensionHelper::getConfig('virtualizor', 'key');
        $pass = ExtensionHelper::getConfig('virtualizor', 'pass');
        $ip = ExtensionHelper::getConfig('virtualizor', 'ip');
        $port = ExtensionHelper::getConfig('virtualizor', 'port');
        $admin = new Virtualizor_Admin_API();
        $admin->Virtualizor_Admin_API($ip, $key, $pass, $port);
        // Get plan ID
        $page = 1;
        $reslen = 1;
        $post = [];
        $post['planname'] = $params['planname'];
        $post['ptype'] = $params['virt'];
        $plans = $admin->plans($page, $reslen, $post);
        if (!key($plans['plans'])) {
            ExtensionHelper::error('Virtualizor', 'Plan not found');
            return;
        }
        $plan = $plans['plans'][key($plans['plans'])];
        // Create server
        $post = [];
        $post['virt'] = $params['virt'];
        $post['user_email'] = $user->email;
        $post['user_pass'] = $config['password'];
        $post['fname'] = $user->name;
        $post['lname'] = $user->name;
        $post['osid'] = 909;
        $post['server_group'] = 0;
        $post['hostname'] = $config['hostname'];
        $post['rootpass'] = $config['password'];
        $post['num_ips6'] = isset($configurableOptions['ips6']) ? $configurableOptions['ips6'] : $plan['ips6'];
        $post['num_ips6_subnet'] = isset($configurableOptions['ips6_subnet']) ? $configurableOptions['ips6_subnet'] : $plan['ips6_subnet'];
        $post['num_ips'] = isset($configurableOptions['ips']) ? $configurableOptions['ips'] : $plan['ips'];
        $post['ram'] = isset($configurableOptions['ram']) ? $configurableOptions['ram'] : $plan['ram'];
        $post['swapram'] = isset($configurableOptions['swap']) ? $configurableOptions['swap'] : $plan['swap'];
        $post['bandwidth'] = isset($configurableOptions['bandwidth']) ? $configurableOptions['bandwidth'] : $plan['bandwidth'];
        $post['network_speed'] = isset($configurableOptions['network_speed']) ? $configurableOptions['network_speed'] : $plan['network_speed'];
        $post['cpu'] = isset($configurableOptions['cpu']) ? $configurableOptions['cpu'] : $plan['cpu'];
        $post['cores'] = isset($configurableOptions['cores']) ? $configurableOptions['cores'] : $plan['cores'];
        $post['cpu_percent'] = isset($configurableOptions['cpu_percent']) ? $configurableOptions['cpu_percent'] : $plan['cpu_percent'];
        $post['vnc'] = isset($configurableOptions['vnc']) ? $configurableOptions['vnc'] : $plan['vnc'];
        $post['vncpass'] = $config['password'];
        $post['kvm_cache'] = $plan['kvm_cache'];
        $post['io_mode'] = $plan['io_mode'];
        $post['vnc_keymap'] = $plan['vnc_keymap'];
        $post['nic_type'] = $plan['nic_type'];
        $post['osreinstall_limit'] = isset($configurableOptions['osreinstall_limit']) ? $configurableOptions['osreinstall_limit'] : $plan['osreinstall_limit'];
        $post['space'] = isset($configurableOptions['space']) ? $configurableOptions['space'] : $plan['space'];

        $output = $admin->addvs_v2($post);

        if (isset($output['error'])) {
            ExtensionHelper::error('Virtualizor', $output['error']);
            return;
        }
        // Set server ID
        $server = $output['vs_info']['vpsid'];
        ExtensionHelper::setOrderProductConfig('external_id', $server, $product->id);

        return true;
    }

    public function suspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $key = ExtensionHelper::getConfig('virtualizor', 'key');
        $pass = ExtensionHelper::getConfig('virtualizor', 'pass');
        $ip = ExtensionHelper::getConfig('virtualizor', 'ip');
        $port = ExtensionHelper::getConfig('virtualizor', 'port');
        $admin = new Virtualizor_Admin_API();
        $admin->Virtualizor_Admin_API($ip, $key, $pass, $port);
        if (!isset($params['config']['external_id'])) {
            ExtensionHelper::error('Virtualizor', 'Server not found for order #' . $order->id . '.');
            return;
        }
        $admin->suspend($params['config']['external_id']);

        return true;
    }

    public function unsuspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $key = ExtensionHelper::getConfig('virtualizor', 'key');
        $pass = ExtensionHelper::getConfig('virtualizor', 'pass');
        $ip = ExtensionHelper::getConfig('virtualizor', 'ip');
        $port = ExtensionHelper::getConfig('virtualizor', 'port');
        $admin = new Virtualizor_Admin_API();
        $admin->Virtualizor_Admin_API($ip, $key, $pass, $port);
        if (!isset($params['config']['external_id'])) {
            ExtensionHelper::error('Virtualizor', 'Server not found for order #' . $order->id . '.');
            return;
        }
        $admin->unsuspend($params['config']['external_id']);

        return true;
    }

    public function terminateServer($user, $params, $order, $product, $configurableOptions)
    {
        $key = ExtensionHelper::getConfig('virtualizor', 'key');
        $pass = ExtensionHelper::getConfig('virtualizor', 'pass');
        $ip = ExtensionHelper::getConfig('virtualizor', 'ip');
        $port = ExtensionHelper::getConfig('virtualizor', 'port');
        $admin = new Virtualizor_Admin_API();
        $admin->Virtualizor_Admin_API($ip, $key, $pass, $port);
        if (!isset($params['config']['external_id'])) {
            ExtensionHelper::error('Virtualizor', 'Server not found for order #' . $order->id . '.');
            return;
        }
        $admin->delete_vs($params['config']['external_id']);

        return true;
    }
}
