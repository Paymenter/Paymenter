<?php

use Illuminate\Support\Facades\Http;
use App\Helpers\ExtensionHelper;

function pteroConfig($key)
{
    $config = ExtensionHelper::getConfig('Pterodactyl', $key);
    if ($config) {
        return $config;
    }
    return null;
}

function Pterodactyl_getConfig()
{
    return [
        [
            "name" => "host",
            "friendlyName" => "Pterodactyl panel url",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "apiKey",
            "friendlyName" => "API Key",
            "type" => "text",
            "required" => true
        ]
    ];
}

function Pterodactyl_getProductConfig()
{
    return [
        [
            "name" => "node",
            "friendlyName" => "Pterodactyl Node",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "location",
            "friendlyName" => "Pterodactyl Location",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "egg",
            "friendlyName" => "Pterodactyl Egg ID",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "nest",
            "friendlyName" => "Pterodactyl Nest ID",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "memory",
            "friendlyName" => "Pterodactyl Memory",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "swap",
            "friendlyName" => "Pterodactyl Swap",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "disk",
            "friendlyName" => "Pterodactyl Disk",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "io",
            "friendlyName" => "Pterodactyl IO",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "cpu",
            "friendlyName" => "CPU limit",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "databases",
            "friendlyName" => "Pterodactyl Database",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "backups",
            "friendlyName" => "Pterodactyl Backups",
            "type" => "text",
            "required" => true
        ],
        [
            "name" => "skip_scripts",
            "friendlyName" => "Pterodactyl Skip Scripts",
            "type" => "boolean",
            "required" => true
        ],
        [
            "name" => "allocation",
            "friendlyName" => "Pterodactyl Allocation",
            "type" => "text",
            "required" => true
        ]
    ];
}

function Pterodactyl_postRequest($url, $data)
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . pteroConfig('apiKey'),
        'Accept' => 'Application/vnd.Pterodactyl.v1+json',
        'Content-Type' => 'application/json'
    ])->post($url, $data);
    return $response;
}

function Pterodactyl_getRequest($url)
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . pteroConfig('apiKey'),
        'Accept' => 'Application/vnd.Pterodactyl.v1+json',
        'Content-Type' => 'application/json'
    ])->get($url);
    return $response;
}

function Pterodactyl_deleteRequest($url)
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . pteroConfig('apiKey'),
        'Accept' => 'Application/vnd.Pterodactyl.v1+json',
        'Content-Type' => 'application/json'
    ])->delete($url);
    return $response;
}

function Pterodactyl_createServer($user, $parmas, $order)
{
    if (Pterodactyl_serverExists($order->id)) {
        return;
    }
    $url = pteroConfig('host') . '/api/application/servers';
    $eggData = Pterodactyl_getRequest(pteroConfig('host') . '/api/application/nests/' . $parmas['nest'] . '/eggs/' . $parmas['egg'] . '?include=variables')->json();
    if (!isset($eggData['attributes'])) {
        return;
    }
    foreach ($eggData['attributes']['relationships']['variables']['data'] as $key => $val) {
        $attr = $val['attributes'];
        $var = $attr['env_variable'];
        $default = $attr['default_value'];
        $environment[$var] = $default;
    }
    $json = [
        'name' => Pterodactyl_random_string(8) . '-' . $order->id,
        'user' => (int) Pterodactyl_getUser($user),
        'egg' => (int) $parmas['egg'],
        'docker_image' => $eggData['attributes']['docker_image'],
        'startup' => $eggData['attributes']['startup'],
        'limits' => [
            'memory' => (int) $parmas['memory'],
            'swap' => (int) $parmas['swap'],
            'disk' => (int) $parmas['disk'],
            'io' => (int) $parmas['io'],
            'cpu' => (int) $parmas['cpu']
        ],
        'feature_limits' => [
            'databases' => $parmas['databases'] ? (int) $parmas['databases'] : null,
            'allocations' => $parmas['allocation'],
            'backups' => $parmas['backups']
        ],
        'deploy' => [
            'locations' => [$parmas['location']],
            'dedicated_ip' => false,
            'port_range' => []
        ],
        'environment' => $environment,
        'external_id' => (string) $order->id,
    ];
    Pterodactyl_postRequest($url, $json);
    return true;
}

function Pterodactyl_random_string($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function Pterodactyl_getUser($user)
{
    $url = pteroConfig('host') . '/api/application/users?filter%5Bemail%5D=' . $user->email;
    $response = Pterodactyl_getRequest($url);
    $users = $response->json();
    if (count($users['data']) > 0) {
        return $users['data'][0]['attributes']['id'];
    } else {
        $url = pteroConfig('host') . '/api/application/users';
        $json = [
            'username' => Pterodactyl_random_string(8),
            'email' => $user->email,
            'first_name' => $user->name,
            'last_name' => 'User',
            'language' => 'en',
            'root_admin' => false,
            'password' => Pterodactyl_random_string(8),
            'password_confirmation' => Pterodactyl_random_string(8)
        ];
        $response = Pterodactyl_postRequest($url, $json);
        $user = $response->json();
        return $user['attributes']['id'];
    }
}

function Pterodactyl_serverExists($order)
{
    $url = pteroConfig('host') . '/api/application/servers/external/' . $order;
    $response = Pterodactyl_getRequest($url);
    $code = $response->status();
    if ($code == 200) {
        return $response->json()['attributes']['id'];
    }
    return false;
}

function Pterodactyl_suspendServer($user, $params, $order)
{
    $server = Pterodactyl_serverExists($order->id);
    if ($server) {
        $url = pteroConfig('host') . '/api/application/servers/' . $server . '/suspend';
        Pterodactyl_postRequest($url, []);
        return true;
    }
    return false;
}

function Pterodactyl_unsuspendServer($user, $params, $order)
{
    $server = Pterodactyl_serverExists($order->id);
    if ($server) {
        $url = pteroConfig('host') . '/api/application/servers/' . $server . '/unsuspend';
        Pterodactyl_postRequest($url, []);
        return true;
    }
    return false;
}

function Pterodactyl_terminateServer($user, $params, $order)
{
    $server = Pterodactyl_serverExists($order->id);
    if ($server) {
        $url = pteroConfig('host') . '/api/application/servers/' . $server;
        Pterodactyl_deleteRequest($url);
        return true;
    }
    return false;
}
