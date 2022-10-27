<?php

use Illuminate\Support\Facades\Http;
use App\Helpers\ExtensionHelper;

function config($key)
{
    $config = ExtensionHelper::getConfig('Pterodactyl', $key);
    if ($config) {
        return $config->value;
    }
    return null;
}

function postRequest($url, $data)
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . config('api_key'),
        'Accept' => 'Application/vnd.pterodactyl.v1+json',
        'Content-Type' => 'application/json'
    ])->post($url, $data);
    return $response;
}

function createServer($parmas)
{   

    $url = config('url') . '/api/application/servers';
    $json = [
        'name' => $parmas['name'],
        'user' => $parmas['user'],
        'external_id' => $parmas['external_id'],
        'description' => $parmas['description'],
        'nest' => $parmas['nest'],
        'egg' => $parmas['egg'],
        'docker_image' => $parmas['docker_image'],
        'startup' => $parmas['startup'],
        'limits' => [
            'memory' => $parmas['memory'],
            'swap' => $parmas['swap'],
            'disk' => $parmas['disk'],
            'io' => $parmas['io'],
            'cpu' => $parmas['cpu']
        ],
        'feature_limits' => [
            'databases' => $parmas['databases'],
            'allocations' => $parmas['allocations']
        ],
        'environment' => $parmas['environment'],
        'deploy' => [
            'locations' => $parmas['locations'],
            'dedicated_ip' => $parmas['dedicated_ip'],
            'port_range' => $parmas['port_range']
        ],
        'start_on_completion' => $parmas['start_on_completion'],
        'skip_scripts' => $parmas['skip_scripts']
    ];

    $response = postRequest($url, $parmas);
    return $response;
}
