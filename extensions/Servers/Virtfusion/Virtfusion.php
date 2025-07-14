<?php

namespace Paymenter\Extensions\Servers\Virtfusion;

use App\Classes\Extension\Server;
use App\Models\Service;
use Exception;
use Illuminate\Support\Facades\Http;

class Virtfusion extends Server
{
    /**
     * Get all the configuration for the extension
     *
     * @param  array  $values
     */
    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'host',
                'type' => 'text',
                'label' => 'Hostname',
                'required' => true,
                'validation' => 'url',
            ],
            [
                'name' => 'apikey',
                'type' => 'text',
                'label' => 'API key',
                'required' => true,
                'encrypted' => true,
            ],
        ];
    }

    /**
     * Get product config
     *
     * @param  array  $values
     */
    public function getProductConfig($values = []): array
    {
        $apiPackages = $this->request('/packages');
        $packages = [];
        foreach ($apiPackages['data'] as $package) {
            $packages[$package['id']] = $package['name'];
        }

        // We need to wait for a virtfusion update to get hypervisors
        $apiHypervisors = $this->request('/compute/hypervisors/groups');
        $hypervisors = [];
        foreach ($apiHypervisors['data'] as $hypervisor) {
            $hypervisors[$hypervisor['id']] = $hypervisor['name'];
        }

        return [
            [
                'name' => 'package',
                'type' => 'select',
                'label' => 'Package',
                'required' => true,
                'options' => $packages,
            ],
            [
                'name' => 'hypervisor',
                'type' => 'select',
                'label' => 'Hypervisor Group ID',
                'required' => true,
                'description' => 'The default Hypervisor group ID',
                'options' => $hypervisors,
            ],
            [
                'name' => 'ipv4',
                'type' => 'number',
                'label' => 'Default IPv4',
                'description' => 'The default amount of IPv4 addresses to assign to the server',
                'required' => true,
                'validation' => 'integer|min:1',
            ],
        ];
    }

    /**
     * Check if currenct configuration is valid
     */
    public function testConfig(): bool|string
    {
        try {
            $this->request('/connect');
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Do a request to the Virtfusion API
     *
     * @param  string  $url
     * @param  string  $method
     * @param  array  $data
     */
    public function request($url, $method = 'get', $data = []): array
    {
        // Trim any leading slashes from the base url and add the path URL to it
        $req_url = rtrim($this->config('host'), '/') . '/api/v1' . $url;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('apikey'),
            'Accept' => 'application/json',
        ])->$method($req_url, $data);

        if (!$response->successful()) {
            throw new Exception('An error occurred, got status code ' . $response->status() . ' on ' . $req_url);
        }

        return $response->json() ?? [];
    }

    /**
     * Get or create a user on VirtFusion
     */
    public function getUser(Service $service): string
    {
        try {
            $response = $this->request('/users/' . $service->user->id . '/byExtRelation');
        } catch (Exception $e) {
            try {
                $response = $this->request('/users', 'post', [
                    'email' => $service->user->email,
                    'name' => $service->user->name,
                    'extRelationId' => $service->user->id,
                ]);
            } catch (Exception $e) {
                throw new Exception('Failed to create user, this is probably due to a wrong extRelationId');
            }
        }

        return $response['data']['id'] ?? '';
    }

    /**
     * Create a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function createServer(Service $service, $settings, $properties)
    {
        if (isset($properties['server_id'])) {
            throw new Exception('Server already exists');
        }

        $data = [
            'packageId' => $settings['package'],
            'userId' => $this->getUser($service),
            'hypervisorId' => $settings['hypervisor'],
            'ipv4' => $settings['ipv4'],
        ];
        // Allowed data to be overwritten
        $allowed = ['ipv4', 'packageId', 'hypervisorId', 'storage', 'memory', 'traffic', 'networkSpeedInbound', 'networkSpeedOutbound', 'cpuCores', 'networkProfile', 'storageProfile'];
        $settings = array_merge($settings, $properties);
        $settings = array_intersect_key($settings, array_flip($allowed));
        $data = array_merge($data, $settings);

        $response = $this->request('/servers', 'post', $data);

        $service->properties()->updateOrCreate([
            'key' => 'server_id',
        ], [
            'name' => 'VirtFusion Server ID',
            'value' => $response['data']['id'],
        ]);

        return $response['data'];
    }

    /**
     * Suspend a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function suspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }

        $this->request('/servers/' . $properties['server_id'] . '/suspend', 'post');

        return true;
    }

    /**
     * Unsuspend a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function unsuspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }

        $this->request('/servers/' . $properties['server_id'] . '/unsuspend', 'post');

        return true;
    }

    /**
     * Terminate a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function terminateServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }

        $this->request('/servers/' . $properties['server_id'], 'delete');

        return true;
    }

    public function getActions(Service $service): array
    {
        return [
            [
                'type' => 'button',
                'label' => 'Go to Server',
                'function' => 'ssoLink',
            ],
        ];
    }

    public function ssoLink(Service $service): string
    {
        $data = $this->request('/users/' . $service->user->id . '/authenticationTokens', 'post');

        return rtrim($this->config('host'), '/') . $data['data']['authentication']['endpoint_complete'];
    }

    public function migrateOption(string $key, ?string $value)
    {
        return match ($key) {
            'ips' => ['key' => 'ipv4', 'value' => $value],
            default => ['key' => $key, 'value' => $value]
        };
    }
}
