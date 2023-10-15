<?php

namespace App\Extensions\Servers\VirtFusion;

use App\Classes\Extensions\Server;
use App\Helpers\ExtensionHelper;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VirtFusion extends Server
{
    public function getMetadata()
    {
        return [
            'display_name' => 'VirtFusion',
            'version' => '1.0.0',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }
    
    public function getConfig()
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

    public function getProductConfig($options)
    {
        $packages = $this->getRequest('/api/v1/packages');
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

    public function createServer($user, $params, $order, $product, $configurableOptions)
    {
        $package = $params['package'];

        $user = $this->getUser($user);
        $response = $this->postRequest(
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

    private function getRequest($url)
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

    private function postRequest($url, $data)
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

    private function getUser($user)
    {
        $response = $this->getRequest('/api/v1/users/' . $user->id . '/byExtRelation');

        if (isset($response->json()['data'])) {
            return $response->json()['data']['id'];
        } else {
            // Create user
            $response = $this->postRequest(
                '/api/v1/users',
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'extRelationId' => $user->id,
                ]
            );

            if ($response->successful()) {
                return $response->json()['data']['id'];
            } else {
                ExtensionHelper::error('VirtFusion', 'Failed to create user ', (string) $response->json() . ' ' . $response->status());

                return;
            }
        }
    }

    public function suspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $apikey = ExtensionHelper::getConfig('VirtFusion', 'apikey');
        $host = ExtensionHelper::getConfig('VirtFusion', 'host');
        if (!isset($params['config']['server_id'])) {
            return;
        }
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

    public function unsuspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $apikey = ExtensionHelper::getConfig('VirtFusion', 'apikey');
        $host = ExtensionHelper::getConfig('VirtFusion', 'host');
        if (!isset($params['config']['server_id'])) {
            return;
        }
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

    public function terminateServer($user, $params, $order, $product, $configurableOptions)
    {
        $apikey = ExtensionHelper::getConfig('VirtFusion', 'apikey');
        $host = ExtensionHelper::getConfig('VirtFusion', 'host');
        if (!isset($params['config']['server_id'])) {
            return;
        }
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

    public function getCustomPages($user, $params, $order, $product, $configurableOptions)
    {
        $apikey = ExtensionHelper::getConfig('VirtFusion', 'apikey');
        $host = ExtensionHelper::getConfig('VirtFusion', 'host');
        if (!isset($params['config']['server_id'])) {
            return;
        }
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

    public function login(OrderProduct $id, Request $request)
    {

        if (!ExtensionHelper::hasAccess($id, auth()->user())) {
            return response()->json(['error' => 'You do not have access to this server'], 403);
        }
        $params = ExtensionHelper::getParameters($id)->config;

        $loginLink = $this->postRequest(
            '/api/v1/users/' . auth()->user()->id . '/serverAuthenticationTokens/' . $params['config']['server_id'],
            []
        );

        $loginLink = $loginLink->json()['data']['authentication']['endpoint_complete'];

        return redirect(ExtensionHelper::getConfig('VirtFusion', 'host') . $loginLink);
    }
}
