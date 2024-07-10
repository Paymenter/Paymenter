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
            'version' => '1.2.0',
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
                'friendlyName' => 'Default Hypervisor Group ID (if no hypervisorId Configurable Options)',
                'required' => true,
            ],
        ];
    }

    public function createServer($user, $params, $order, $product, $configurableOptions)
    {
        // Determine package ID based on configurable options or fallback to default
        $package = $configurableOptions['package'] ?? $params['package'];

        // Get user ID and check for errors
        $user = $this->getUser($user);
        if (!$user) {
            ExtensionHelper::error('VirtFusion', 'Failed to retrieve user.');
            return false;
        }

        // Prepare basic data for the POST request
        $requestData = [
            'packageId' => $package,
            'userId' => $user,
            'hypervisorId' => $configurableOptions['hypervisorId'] ?? $params['hypervisor']
        ];

        // Define optional configurable options and their respective keys
        $optionalFields = [
            'hypervisorId',
            'ipv4',
            'storage',
            'traffic',
            'memory',
            'cpuCores', 
            'networkSpeedInbound',
            'networkSpeedOutbound',
            'storageProfile', 
            'networkProfile',
            'firewallRulesets',
            'additionalStorage1Enable', 
            'additionalStorage2Enable',
            'additionalStorage1Profile', 
            'additionalStorage2Profile',
            'additionalStorage1Capacity', 
            'additionalStorage2Capacity'
        ];

        // Filter and merge optional fields into requestData
        $requestData = array_merge($requestData, array_filter($configurableOptions, function($key) use ($optionalFields) {
            return in_array($key, $optionalFields);
        }, ARRAY_FILTER_USE_KEY));

        // Make the POST request to create the server
        $response = $this->postRequest('/api/v1/servers', $requestData);
        $responseData = $response->json();

        // Check for errors in the response
        if (isset($responseData['errors'])) {
            // Convert errors array to a string
            $error = implode(" ", $responseData['errors']);
            ExtensionHelper::error('VirtFusion', 'Failed to create server: ' . $error);
            return false; // Return false to indicate failure
        }

        // Set server_id in order product configuration
        if (isset($responseData['data']['id'])) {
            ExtensionHelper::setOrderProductConfig('server_id', $responseData['data']['id'], $product->id);
            return true; // Return true to indicate success
        } else {
            ExtensionHelper::error('VirtFusion', 'Server created but missing ID.');
            return false;
        }
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
