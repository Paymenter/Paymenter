<?php

namespace Paymenter\Extensions\Servers\CPanel;

use App\Classes\Extension\Server;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use App\Rules\Domain;
use Illuminate\Support\Str;

class CPanel extends Server
{
    private function request($endpoint, $method = 'get', $data = [])
    {
        $host = rtrim($this->config('host'), '/');
        $response = Http::withHeaders([
            'Authorization' => 'whm ' . $this->config('username') . ':' . $this->config('apikey'),
        ])->$method($host . '/json-api' . $endpoint, $data);

        if ($response->failed()) {
            throw new \Exception('Error while requesting API');
        }
        return $response;
    }


    /**
     * Get all the configuration for the extension
     * 
     * @param array $values
     * @return array
     */
    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'host',
                'type' => 'text',
                'label' => 'Hostname',
                'placeholder' => 'https://example.com:2087',
                'validation' => 'url:http,https',
                'required' => true,
            ],
            [
                'name' => 'username',
                'type' => 'text',
                'placeholder' => 'johndoe',
                'label' => 'Username',
                'required' => true,
            ],
            [
                'name' => 'apikey',
                'type' => 'text',
                'placeholder' => '1234567890abcdef',
                'label' => 'API key',
                'required' => true,
            ],
        ];
    }

    /**
     * Get product config
     * 
     * @param array $values
     * @return array
     */
    public function getProductConfig($values = []): array
    {
        // Get all the packages
        $packages = $this->request('/listpkgs')->json();
        $packageOptions = [];
        foreach ($packages['package'] as $package) {
            $packageOptions[] = [
                'value' => $package['name'],
                'label' => $package['name'],
            ];
        }

        return [
            [
                'name' => 'package',
                'type' => 'select',
                'label' => 'Package',
                'options' => $packageOptions,
                'required' => true,
            ],
        ];
    }

    /**
     * Check if currenct configuration is valid
     *
     * @return bool|string
     */
    public function testConfig(): bool|string
    {
        $request = $this->request('/listaccts');
        if (!$request->successful()) {
            return $request->json('statusmsg');
        }

        return true;
    }

    public function getCheckoutConfig()
    {
        return [
            [
                'name' => 'domain',
                'type' => 'text',
                'label' => 'Domain',
                'required' => true,
                'validation' => [new Domain, 'required'],
                'placeholder' => 'domain.com',
            ],
        ];
    }


    /**
     * Create a server 
     * 
     * @param Service $service
     * @param array $settings (product settings)
     * @param array $properties (checkout options)
     * @return bool
     */
    public function createServer(Service $service, $settings, $properties)
    {
        $username = Str::random();
        // If first one is a number, add a letter
        if (is_numeric($username[0])) {
            $username = 'a' . substr($username, 1);
        }

        $response = $this->request(
            '/createacct',
            data: [
                'api.version' => 1,
                'username' => $username,
                'contactemail' => $service->user->email,
                'domain' => $properties['domain'],
                'plan' => $properties['package'] ?? $settings['package'],
            ]
        );

        if ($response->json()['metadata']['result'] === 1) {
            $service->properties()->updateOrCreate([
                'key' => 'cpanel_username',
            ], [
                'name' => 'cPanel username',
                'value' => strtolower($username),
            ]);
        }

        return true;
    }

    /**
     * Suspend a server
     * 
     * @param Service $service
     * @param array $settings (product settings)
     * @param array $properties (checkout options)
     * @return bool
     */
    public function suspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['cpanel_username'])) {
            throw new \Exception('Service has not been created');
        }

        $response = $this->request(
            '/suspendacct',
            data: [
                'api.version' => 1,
                'user' => $properties['cpanel_username'],
            ]
        );

        if ($response->json()['metadata']['result'] === 1) {
            return true;
        }

        return false;
    }

    /**
     * Unsuspend a server
     * 
     * @param Service $service
     * @param array $settings (product settings)
     * @param array $properties (checkout options)
     * @return bool
     */
    public function unsuspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['cpanel_username'])) {
            throw new \Exception('Service has not been created');
        }

        $response = $this->request(
            '/unsuspendacct',
            data: [
                'api.version' => 1,
                'user' => $properties['cpanel_username'],
            ]
        );

        if ($response->json()['metadata']['result'] === 1) {
            return true;
        }

        return false;
    }

    /**
     * Terminate a server
     * 
     * @param Service $service
     * @param array $settings (product settings)
     * @param array $properties (checkout options)
     * @return bool
     */
    public function terminateServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['cpanel_username'])) {
            throw new \Exception('Service has not been created');
        }

        $response = $this->request(
            '/removeacct',
            data: [
                'api.version' => 1,
                'user' => $properties['cpanel_username'],
            ]
        );

        if ($response->json()['metadata']['result'] === 1) {
            // Delete the properties
            $service->properties()->where('key', 'cpanel_username')->delete();
            return true;
        }

        return false;
    }

    public function upgradeServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['cpanel_username'])) {
            throw new \Exception('Service has not been created');
        }

        $response = $this->request(
            '/modifyacct',
            data: [
                'api.version' => 1,
                'user' => $properties['cpanel_username'],
                'plan' => $settings['package'],
            ]
        );

        if ($response->json()['metadata']['result'] === 1) {
            return true;
        }

        return false;
    }
}
