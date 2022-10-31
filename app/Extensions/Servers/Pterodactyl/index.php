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

function pterodactyl_postRequest($url, $data)
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . pteroConfig('apiKey'),
        'Accept' => 'Application/vnd.pterodactyl.v1+json',
        'Content-Type' => 'application/json'
    ])->post($url, $data);
    return $response;
}

function pterodactyl_getRequest($url)
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . pteroConfig('apiKey'),
        'Accept' => 'Application/vnd.pterodactyl.v1+json',
        'Content-Type' => 'application/json'
    ])->get($url);
    return $response;
}

function pterodactyl_deleteRequest($url)
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . pteroConfig('apiKey'),
        'Accept' => 'Application/vnd.pterodactyl.v1+json',
        'Content-Type' => 'application/json'
    ])->delete($url);
    return $response;
}

function createServer($user, $parmas, $order)
{
    if(pterodactyl_serverExists($order->id)) {
        return;
    }
    $url = pteroConfig('host') . '/api/application/servers';
    $eggData = pterodactyl_getRequest(pteroConfig('host') . '/api/application/nests/' . $parmas['nest'] . '/eggs/' . $parmas['egg'] . '?include=variables')->json();
    foreach ($eggData['attributes']['relationships']['variables']['data'] as $key => $val) {
        $attr = $val['attributes'];
        $var = $attr['env_variable'];
        $default = $attr['default_value'];
        $environment[$var] = $default;
    }
    $json = [
        'name' => pterodactyl_random_string(8) . '-' . $order->id,
        'user' => (int) pterodactyl_getUser($user),
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
            'locations' => [ $parmas['location'] ],
            'dedicated_ip' => false,
            'port_range' => [ ]
        ],
        'environment' => $environment,
        'external_id' => (string) $order->id,
    ];
    pterodactyl_postRequest($url, $json);
    return true;
}

function pterodactyl_random_string($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function pterodactyl_getUser($user)
{
    $url = pteroConfig('host') . '/api/application/users?filter%5Bemail%5D=' . $user->email;
    $response = pterodactyl_getRequest($url);
    $users = $response->json();
    if (count($users['data']) > 0) {
        return $users['data'][0]['attributes']['id'];
    }else{
        $url = pteroConfig('host') . '/api/application/users';
        $json = [
            'username' => pterodactyl_random_string(8),
            'email' => $user->email,
            'first_name' => $user->name,
            'last_name' => 'User',
            'language' => 'en',
            'root_admin' => false,
            'password' => pterodactyl_random_string(8),
            'password_confirmation' => pterodactyl_random_string(8)
        ];
        $response = pterodactyl_postRequest($url, $json);
        $user = $response->json();
        return $user['attributes']['id'];
    }
}

function pterodactyl_serverExists($order)
{
    $url = pteroConfig('host') . '/api/application/servers/external/' . $order;
    $response = pterodactyl_getRequest($url);
    $code = $response->status();
    if ($code == 200) {
        return $response->json()['attributes']['id'];
    }
    return false;
}

function suspendServer($order)
{
    $server = pterodactyl_serverExists($order->id);
    if ($server) {
        $url = pteroConfig('host') . '/api/application/servers/' . $server . '/suspend';
        $response = pterodactyl_postRequest($url, []);
        error_log($response->status());
        return true;
    }
    return false;

}

function unsuspendServer($order)
{
    $server = pterodactyl_serverExists($order->id);
    if ($server) {
        $url = pteroConfig('host') . '/api/application/servers/' . $server . '/unsuspend';
        $response = pterodactyl_postRequest($url, []);
        error_log($response->status());
        return true;
    }
    return false;
}

function terminateServer($order)
{
    $server = pterodactyl_serverExists($order->id);
    if ($server) {
        $url = pteroConfig('host') . '/api/application/servers/' . $server;
        $response = pterodactyl_deleteRequest($url);
        error_log($response->status());
        return true;
    }
    return false;
}