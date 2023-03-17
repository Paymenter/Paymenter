<?php

use App\Models\Product;
use App\Helpers\ExtensionHelper;

require_once __DIR__ . '/sdk.php';

function Virtualizor_getConfig()
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

function Virtualizor_getUserConfig(Product $product)
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

function Virtualizor_getProductConfig()
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

function Virtualizor_createServer($user, $params, $order)
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
    $post['num_ips6'] = $plan['ips6'];
    $post['num_ips6_subnet'] = $plan['ips6_subnet'];
    $post['num_ips'] = $plan['ips'];
    $post['ram'] = $plan['ram'];
    $post['swapram'] = $plan['swap'];
    $post['bandwidth'] = $plan['bandwidth'];
    $post['network_speed'] = $plan['network_speed'];
    $post['cpu'] = $plan['cpu'];
    $post['cores'] = $plan['cores'];
    $post['cpu_percent'] = $plan['cpu_percent'];
    $post['vnc'] = $plan['vnc'];
    $post['vncpass'] = $config['password'];
    $post['kvm_cache'] = $plan['kvm_cache'];
    $post['io_mode'] = $plan['io_mode'];
    $post['vnc_keymap'] = $plan['vnc_keymap'];
    $post['nic_type'] = $plan['nic_type'];
    $post['osreinstall_limit'] = $plan['osreinstall_limit'];
    $post['space'] = $plan['space'];

    $output = $admin->addvs_v2($post);

    if(isset($output['error'])){
        ExtensionHelper::error('Virtualizor', $output['error']);
        return;
    }
    // Set server ID
    $server = $output['vs_info']['vpsid'];
    ExtensionHelper::setOrderProductConfig('external_id', $server, $params['config_id']);

    return true;
}

function Virtualizor_suspendServer($user, $params, $order)
{
    $key = ExtensionHelper::getConfig('virtualizor', 'key');
    $pass = ExtensionHelper::getConfig('virtualizor', 'pass');
    $ip = ExtensionHelper::getConfig('virtualizor', 'ip');
    $port = ExtensionHelper::getConfig('virtualizor', 'port');
    $admin = new Virtualizor_Admin_API();
    $admin->Virtualizor_Admin_API($ip, $key, $pass, $port);
    $output = $admin->suspend($params['config']['external_id']);

    return true;
}

function Virtualizor_unsuspendServer($user, $params, $order)
{
    $key = ExtensionHelper::getConfig('virtualizor', 'key');
    $pass = ExtensionHelper::getConfig('virtualizor', 'pass');
    $ip = ExtensionHelper::getConfig('virtualizor', 'ip');
    $port = ExtensionHelper::getConfig('virtualizor', 'port');
    $admin = new Virtualizor_Admin_API();
    $admin->Virtualizor_Admin_API($ip, $key, $pass, $port);
    $output = $admin->unsuspend($params['config']['external_id']);

    return true;
}

function Virtualizor_terminateServer($user, $params, $order)
{
    $key = ExtensionHelper::getConfig('virtualizor', 'key');
    $pass = ExtensionHelper::getConfig('virtualizor', 'pass');
    $ip = ExtensionHelper::getConfig('virtualizor', 'ip');
    $port = ExtensionHelper::getConfig('virtualizor', 'port');
    $admin = new Virtualizor_Admin_API();
    $admin->Virtualizor_Admin_API($ip, $key, $pass, $port);
    $output = $admin->delete_vs($params['config']['external_id']);

    return true;
}
