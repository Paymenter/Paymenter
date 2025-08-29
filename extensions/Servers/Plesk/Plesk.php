<?php

namespace Paymenter\Extensions\Servers\Plesk;

use App\Classes\Extension\Server;
use App\Models\Service;
use App\Rules\Domain;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Plesk extends Server
{
    private function request($endpoint, $method = 'get', $data = [])
    {
        $host = rtrim($this->config('host'), '/');
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->withBasicAuth($this->config('username'), $this->config('password'))->withoutVerifying()->$method($host . '/api/v2' . $endpoint, $data)->throw();

        return $response;
    }

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
                'label' => 'Host',
                'type' => 'text',
                'required' => true,
                'description' => 'The IP address or domain name of the Plesk server (with http:// or https://)',
            ],
            [
                'name' => 'username',
                'label' => 'Username',
                'type' => 'text',
                'required' => true,
                'description' => 'The username of the Plesk server',
            ],
            [
                'name' => 'password',
                'label' => 'Password',
                'type' => 'password',
                'required' => true,
                'description' => 'The password of the Plesk server',
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
        return [
            [
                'name' => 'plan',
                'type' => 'text',
                'label' => 'Plan',
                'required' => true,
                'description' => 'The plan name of the wanted service plan',
            ],
        ];
    }

    /**
     * Check if currenct configuration is valid
     */
    public function testConfig(): bool|string
    {
        try {
            $this->request('/server', 'get')->json();

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
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
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function createServer(Service $service, $settings, $properties)
    {
        $returnData = [
            'domain' => $properties['domain'],
            'username' => strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $service->user->name)),
            'ftp_password' => Str::password(12),
        ];

        $pleskCustomerId = $service->user->properties->where('key', 'plesk_id')->first();

        try {
            $user = $this->request('/clients/' . $pleskCustomerId->value)->json();
            // Check if user exists
        } catch (Exception $e) {
            $returnData['password'] = Str::password(12);

            $user = $this->request('/clients', 'post', [
                'username' => $returnData['username'],
                'email' => $service->user->email,
                'password' => $returnData['password'],
                'name' => $service->user->name,
                'login' => $returnData['username'],
                'type' => 'customer',
                'external_id' => $service->user->id,
            ])->json();

            $service->user->properties()->create([
                'key' => 'plesk_id',
                'name' => 'Plesk ID',
                'value' => $user['id'],
            ]);
        }

        $returnData['client_id'] = $user['id'];

        $response = $this->request('/domains', 'post', [
            'name' => $properties['domain'],
            'external_id' => $service->id,
            'owner_client' => [
                'id' => $returnData['client_id'],
                'login' => 'owner',
                'guid' => $user['guid'],
                'external_id' => $service->user->id,
            ],
            'hosting_type' => 'virtual',
            'hosting_settings' => [
                'ftp_login' => $returnData['username'],
                'ftp_password' => $returnData['ftp_password'],
            ],
            'plan' => [
                'name' => $settings['plan'],
            ],
        ])->json();

        $service->properties()->updateOrCreate([
            'key' => 'domain_id',
        ], [
            'name' => 'Plesk Domain ID',
            'value' => $response['id'],
        ]);

        return $returnData;
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
        if (!isset($properties['domain_id'])) {
            throw new Exception('Service has not been created');
        }

        $this->request('/domains/' . $properties['domain_id'] . '/status', 'put', [
            'status' => 'suspended',
        ]);

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
        if (!isset($properties['domain_id'])) {
            throw new Exception('Service has not been created');
        }

        $this->request('/domains/' . $properties['domain_id'] . '/status', 'put', [
            'status' => 'active',
        ]);

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
        if (!isset($properties['domain_id'])) {
            throw new Exception('Service has not been created');
        }

        $this->request('/domains/' . $properties['domain_id'], 'delete');

        $service->properties()->where('key', 'domain_id')->delete();

        return true;
    }
}
