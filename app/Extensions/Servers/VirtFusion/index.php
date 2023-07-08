<?php

use App\Helpers\ExtensionHelper;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

function VirtFusion_getConfig()
{
    return [
        [
            'name' => 'host',
            'type' => 'text',
            'friendlyName' => 'Hostname',
            'required' => true,
        ],
        [
            'name' => 'apikey',
            'type' => 'text',
            'friendlyName' => 'API key',
            'required' => true,
        ],
    ];
}

function VirtFusion_getProductConfig()
{
    $packages = VirtFusion_getRequest('/api/v1/packages');
    $package = [];
    foreach ($packages->json()['data'] as $p) {
        $package[] = [
            'name' => $p['name'],
            'value' => $p['id'],
        ];
    }
    return [
        [
            'name' => 'package',
            'type' => 'dropdown',
            'friendlyName' => 'Package',
            'required' => true,
            'options' => $package,
        ],
        [
            'name' => 'hypervisor',
            'type' => 'text',
            'friendlyName' => 'Hypervisor Group ID',
            'required' => true,
        ],
        [
            'name' => 'ips',
            'type' => 'text',
            'friendlyName' => 'Number IPs',
            'required' => true,
        ],
    ];
}

function VirtFusion_createServer($user, $params, $order, $product, $configurableOptions)
{
    $package = $params['package'];

    $user = VirtFusion_getUser($user);
    $response = VirtFusion_postRequest(
        '/api/v1/servers',
        [
            'packageId' => $package,
            'userId' => $user,
            'hypervisorId' => $params['hypervisor'],
            'ipv4' => $params['ips'],
        ]
    );
    if (isset($response->json()['errors'])) {
        // Array to string conversion
        $error = implode(" ", $response->json()['errors']);
        ExtensionHelper::error('VirtFusion', 'Failed to create server' . $error);
        return;
    }
    ExtensionHelper::setOrderProductConfig('server_id', $response->json()['data']['id'], $product->id);

    return true;
}

function VirtFusion_getRequest($url)
{
    $apikey = ExtensionHelper::getConfig('VirtFusion', 'apikey');
    $host = ExtensionHelper::getConfig('VirtFusion', 'host');

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apikey,
        'Accept' => 'Application/json',
        'Content-Type' => 'application/json',
    ])->get(
        $host . $url
    );
    return $response;
}

function VirtFusion_postRequest($url, $data)
{
    $apikey = ExtensionHelper::getConfig('VirtFusion', 'apikey');
    $host = ExtensionHelper::getConfig('VirtFusion', 'host');

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apikey,
        'Accept' => 'Application/json',
        'Content-Type' => 'application/json',
    ])->post(
        $host . $url,
        $data
    );
    return $response;
}

function VirtFusion_getUser($user)
{
    $response = VirtFusion_getRequest('/api/v1/users/' . $user->id . '/byExtRelation');

    if (isset($response->json()['data'])) {
        return $response->json()['data']['id'];
    } else {
        // Create user
        $response = VirtFusion_postRequest(
            '/api/v1/users',
            [
                'name' => $user->name,
                'email' => $user->email,
                'extRelationId' => $user->id,
            ]
        );

        if ($response->status() == 200) {
            return $response->json()['data']['id'];
        } else {
            ExtensionHelper::error('VirtFusion', 'Failed to create user ', (string) $response->json() . ' ' . $response->status());

            return;
        }
    }
}

function VirtFusion_suspendServer($user, $params, $order)
{
    $apikey = ExtensionHelper::getConfig('VirtFusion', 'apikey');
    $host = ExtensionHelper::getConfig('VirtFusion', 'host');
    $server = $params['config']['server_id'];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apikey,
        'Accept' => 'Application/json',
        'Content-Type' => 'application/json',
    ])->post(
        $host . '/api/v1/servers/' . $server . '/suspend'
    );
    if ($response->status() == 204) {
        return true;
    } else {
        ExtensionHelper::error('VirtFusion', 'Failed to suspend server');

        return;
    }

    return true;
}

function VirtFusion_unsuspendServer($user, $params, $order)
{
    $apikey = ExtensionHelper::getConfig('VirtFusion', 'apikey');
    $host = ExtensionHelper::getConfig('VirtFusion', 'host');
    $server = $params['config']['server_id'];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apikey,
        'Accept' => 'Application/json',
        'Content-Type' => 'application/json',
    ])->post(
        $host . '/api/v1/servers/' . $server . '/unsuspend'
    );
    if ($response->status() == 204) {
        return true;
    } else {
        ExtensionHelper::error('VirtFusion', 'Failed to unsuspend server');

        return;
    }

    return true;
}

function VirtFusion_terminateServer($user, $params, $order)
{
    $apikey = ExtensionHelper::getConfig('VirtFusion', 'apikey');
    $host = ExtensionHelper::getConfig('VirtFusion', 'host');
    $server = $params['config']['server_id'];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apikey,
        'Accept' => 'Application/json',
        'Content-Type' => 'application/json',
    ])->delete(
        $host . '/api/v1/servers/' . $server . '?delay=5'
    );
    if ($response->status() == 204) {
        return true;
    } else {
        ExtensionHelper::error('VirtFusion', 'Failed to terminate server');

        return;
    }

    return true;
}

function VirtFusion_getCustomPages($user, $params, $order, $product, $configurableOptions)
{
    $apikey = ExtensionHelper::getConfig('VirtFusion', 'apikey');
    $host = ExtensionHelper::getConfig('VirtFusion', 'host');
    $server = $params['config']['server_id'];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apikey,
        'Accept' => 'Application/json',
        'Content-Type' => 'application/json',
    ])->get(
        $host . '/api/v1/servers/' . $server
    );

    if ($response->status() !== 200) {
        ExtensionHelper::error('VirtFusion', 'Failed to get custom pages');

        return;
    }

    return [
        'name' => 'VirtFusion',
        'template' => 'virtfusion::control',
        'data' => [
            'details' => (object) $response->json()['data'],
        ],
    ];
}

function VirtFusion_getLink($user, $params, $order, $product, $configurableOptions)
{ 
    $apikey = ExtensionHelper::getConfig('VirtFusion', 'apikey');
    $host = ExtensionHelper::getConfig('VirtFusion', 'host');
    $server = $params['config']['server_id'];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apikey,
        'Accept' => 'Application/json',
        'Content-Type' => 'application/json',
    ])->get(
        $host . '/api/v1/servers/' . $server
    );
    return $host . '/server/' . $response->json()['data']['uuid'];
}


function VirtFusion_login(OrderProduct $id, Request $request) {

    if(!ExtensionHelper::hasAccess($id, auth()->user())){
        return response()->json(['error' => 'You do not have access to this server'], 403);
    }
    $params = ExtensionHelper::getParameters($id)->config;

    $loginLink = VirtFusion_postRequest(
        '/api/v1/users/' . auth()->user()->id . '/serverAuthenticationTokens/' . $params['config']['server_id'],
        []
    );

    $loginLink = $loginLink->json()['data']['authentication']['endpoint_complete'];

    return redirect(ExtensionHelper::getConfig('VirtFusion', 'host') . $loginLink);
}