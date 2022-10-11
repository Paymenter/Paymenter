<?php

use Illuminate\Support\Facades\Http;
// Name should be the same as the folder name
$config = 
$name = 'Pterodactyl';
$author = 'Paymenter';
$version = '0.0.1';
$description = 'Pterodactyl Server Management';
$website = 'https://pterodactyl.io/';


function testConnection()
{
    $response = Http::withToken(config('pterodactyl.apiKey'))->get(config('pterodactyl.host') . '/api/application/servers');

    if ($response->successful()) {
        return true;
    }

    return false;
}
function create($parmas)
{
    Http::post(config('pterodactyl.host') . '/api/application/servers', [
        'name' => $params['domain'],
        'user' => $params['username'],
        'external_id' => $params['serviceid'],
        'description' => $params['description'],
        'nest' => $params['configoption1'],
        'egg' => $params['configoption2'],
        'docker_image' => $params['configoption3'],
        'startup' => $params['configoption4'],
        'limits' => [
            'memory' => $params['configoption5'],
            'swap' => $params['configoption6'],
            'disk' => $params['configoption7'],
            'io' => $params['configoption8'],
            'cpu' => $params['configoption9'],
        ],
        'feature_limits' => [
            'databases' => $params['configoption10'],
            'allocations' => $params['configoption11'],
        ],
        'environment' => [
            'SERVER_MEMORY' => $params['configoption5'],
            'SERVER_PORT' => $params['configoption12'],
            'SERVER_IP' => $params['configoption13'],
        ],
        'deploy' => [
            'locations' => [
                $params['configoption14'],
            ],
            'dedicated_ip' => true,
            'port_range' => [
                $params['configoption12'],
            ],
        ],
        'start_on_completion' => true,
        'skip_scripts' => $params['configoption15'],
        'backups' => $params['configoption16'],
    ]);
}
