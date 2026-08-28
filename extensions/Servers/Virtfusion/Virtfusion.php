<?php

namespace Paymenter\Extensions\Servers\Virtfusion;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Server;
use App\Models\Service;
use Exception;
use Illuminate\Support\Facades\Http;

#[ExtensionMeta(
    name: 'Virtfusion',
    description: 'Virtfusion server extension',
    version: 'builtin',
    author: 'Paymenter',
    url: 'https://paymenter.org/docs/extensions/virtfusion',
    icon: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODgiIGhlaWdodD0iODgiIHZpZXdib3g9IjAgMCA4OCA4OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBjbGFzcz0iaG92ZXI6c2NhbGUtMTIwIGZsZXggaXRlbXMtY2VudGVyIGp1c3RpZnktY2VudGVyIHNpemUtMTggcm91bmRlZC14bCBwLTQgYmctWyMxNjZCQkRdIHJpbmctNiByaW5nLVsjMTY2QkJEXS80MCI+PHBhdGggZD0iTTMuNjM1OTcgMTYuNTMxMkMxLjgyNTI3IDE0LjcyMTQgMC45MTk5MjIgMTIuNTQxIDAuOTE5OTIyIDkuOTg5ODVDMC45MTk5MjIgNy40Mzg3NCAxLjgyNTI3IDUuMjU4MjggMy42MzU5NyAzLjQ0ODQ3QzUuNDQ1NzggMS42Mzc3NyA3LjYyNjI0IDAuNzMyNDIyIDEwLjE3NzQgMC43MzI0MjJDMTIuNzI3NiAwLjczMjQyMiAxNC45MDggMS42Mzc3NyAxNi43MTg3IDMuNDQ4NDdMNTAuNjYxMyAzNy4zOTFMNjguODA1MSA1NS41MzQ4VjkuOTg5ODVDNjguODA1MSA3LjQzODc0IDY5LjcxMDQgNS4yNTgyOCA3MS41MjExIDMuNDQ4NDdDNzMuMzMwOSAxLjYzNzc3IDc1LjUxMTQgMC43MzI0MjIgNzguMDYyNSAwLjczMjQyMkM4MC42MTM2IDAuNzMyNDIyIDgyLjc5NDEgMS42Mzc3NyA4NC42MDM5IDMuNDQ4NDdDODYuNDE0NiA1LjI1ODI4IDg3LjMxOTkgNy40Mzg3NCA4Ny4zMTk5IDkuOTg5ODVWNzcuODc1Qzg3LjMxOTkgODAuNDI2MSA4Ni40MTQ2IDgyLjYwNjYgODQuNjAzOSA4NC40MTY0QzgyLjc5NDEgODYuMjI3MSA4MC42MTM2IDg3LjEzMjQgNzguMDYyNSA4Ny4xMzI0Qzc1LjUxMTQgODcuMTMyNCA3My4zMzA5IDg2LjIyNzEgNzEuNTIxMSA4NC40MTY0TDM3LjU3ODUgNTAuNDczOEwzLjYzNTk3IDE2LjUzMTJaIiBmaWxsPSJ3aGl0ZSI+PC9wYXRoPjwvc3ZnPg==',
)]
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
}
