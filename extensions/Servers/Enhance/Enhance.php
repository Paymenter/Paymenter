<?php

namespace Paymenter\Extensions\Servers\Enhance;

use App\Classes\Extension\Server;
use App\Models\Service;
use App\Models\User;
use App\Rules\Domain;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Enhance extends Server
{
    private function request($url, $method = 'get', $data = []): Response
    {
        // Trim any leading slashes from the base url and add the path URL to it
        $req_url = rtrim($this->config('host'), '/') . '/api' . $url;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('apikey'),
            'Accept' => 'application/json',
        ])->$method($req_url, $data);

        if (!$response->successful()) {
            $body = $response->body();
            throw new Exception(
                'An error occurred, got status code ' . $response->status() . ' on ' . $url .
                ($body ? ' | Response: ' . $body : '')
            );
        }

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
                'type' => 'text',
                'label' => 'Hostname',
                'validation' => 'url:http,https',
                'required' => true,
                'placeholder' => 'https://domain.com',
            ],
            [
                'name' => 'apikey',
                'type' => 'text',
                'label' => 'API key',
                'required' => true,
                'placeholder' => '2cd0079b...',
            ],
            [
                'name' => 'orgId',
                'type' => 'text',
                'label' => 'Organization ID',
                'required' => true,
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
        $plans = $this->request('/orgs/' . $this->config('orgId') . '/plans')->json();
        $plans = array_map(function ($plan) {
            return [
                'value' => $plan['id'],
                'label' => $plan['name'] . ' (' . $plan['planType'] . ')',
            ];
        }, $plans['items']);

        return [
            [
                'name' => 'plan',
                'type' => 'select',
                'options' => $plans,
                'label' => 'Enhance package ID',
                'required' => true,
            ],
        ];
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
     * Check if currenct configuration is valid
     */
    public function testConfig(): bool|string
    {
        try {
            $this->request('/orgs/' . $this->config('orgId'));

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function fetchUserOrg(User $user)
    {
        if ($user->properties()->where('key', 'enhance_orgId')->exists()) {
            return $user->properties()->where('key', 'enhance_orgId')->first()->value;
        }
        $response = $this->request('/orgs/' . $this->config('orgId') . '/customers', 'post', [
            'name' => $user->name,
        ])->json();

        if (isset($response['id'])) {
            $user->properties()->create([
                'key' => 'enhance_orgId',
                'value' => $response['id'],
                'name' => 'Enhance Org ID',
            ]);
        }

        // Create Login
        $loginResponse = $this->request('/logins?orgId=' . $response['id'], 'post', [
            'email' => $user->email,
            'name' => $user->name,
            'password' => Str::password(),
        ])->json();

        // Create Org member
        $this->request('/orgs/' . $response['id'] . '/members', 'post', [
            'loginId' => $loginResponse['id'],
            'roles' => ['Owner'],
        ]);

        return $response['id'];
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
        if (isset($properties['subscription_id'])) {
            throw new Exception('Service has already been created');
        }
        $user = $this->fetchUserOrg($service->user);

        $settings = array_merge($settings, $properties);

        $response = $this->request('/orgs/' . $this->config('orgId') . '/customers/' . $user . '/subscriptions', 'post', [
            'planId' => (int) $settings['plan'],
        ])->json();

        if (isset($response['id'])) {
            $service->properties()->updateOrCreate([
                'key' => 'subscription_id',
            ], [
                'name' => 'Enhance Subscription ID',
                'value' => $response['id'],
            ]);
        }

        $data = [
            'domain' => $settings['domain'],
            'subscriptionId' => $response['id'],
        ];

        // Allow serverGroupId (uuid)
        if (isset($settings['location']) && !empty($settings['location'])) {
            $data['serverGroupId'] = $this->findServerGroup($settings['location']);
        }

        // Add domain
        $this->request('/orgs/' . $user . '/websites', 'post', $data);

        return true;
    }

    private function findServerGroup($serverGroupName)
    {
        $response = $this->request('/servers/groups', 'get')->json();

        foreach ($response['items'] as $group) {
            if ($group['name'] === $serverGroupName || $group['id'] === $serverGroupName) {
                return $group['id'];
            }
        }

        throw new Exception('Server group not found');
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
        if (!isset($properties['subscription_id'])) {
            throw new Exception('Service does not exist');
        }

        if (!$service->properties()->where('key', 'subscription_id')->exists()) {
            throw new Exception('Missing user organization ID');
        }

        $this->request('/orgs/' . $service->user->properties()->where('key', 'enhance_orgId')->first()->value . '/subscriptions/' . $properties['subscription_id'], 'patch', [
            'isSuspended' => true,
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
        if (!isset($properties['subscription_id'])) {
            throw new Exception('Service does not exist');
        }

        if (!$service->properties()->where('key', 'subscription_id')->exists()) {
            throw new Exception('Missing user organization ID');
        }

        $this->request('/orgs/' . $service->user->properties()->where('key', 'enhance_orgId')->first()->value . '/subscriptions/' . $properties['subscription_id'], 'patch', [
            'isSuspended' => false,
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
        if (!isset($properties['subscription_id'])) {
            throw new Exception('Service does not exist');
        }

        if (!$service->properties()->where('key', 'subscription_id')->exists()) {
            throw new Exception('Missing user organization ID');
        }

        $this->request('/orgs/' . $service->user->properties()->where('key', 'enhance_orgId')->first()->value . '/subscriptions/' . $properties['subscription_id'], 'delete');

        $service->properties()->where('key', 'subscription_id')->delete();

        return true;
    }

    /**
     * Upgrade a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function upgradeServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['subscription_id'])) {
            throw new Exception('Service does not exist');
        }

        if (!$service->properties()->where('key', 'subscription_id')->exists()) {
            throw new Exception('Missing user organization ID');
        }

        $this->request('/orgs/' . $service->user->properties()->where('key', 'enhance_orgId')->first()->value . '/subscriptions/' . $properties['subscription_id'], 'patch', [
            'planId' => (int) ($properties['plan'] ?? $settings['plan']),
        ]);

        return true;
    }

    public function getActions(Service $service, $settings, $properties): array
    {
        if (!$service->properties()->where('key', 'subscription_id')->exists()) {
            throw new Exception('Missing subscription ID');
        }

        return [
            [
                'type' => 'button',
                'label' => 'Login Control Panel',
                'function' => 'ssoLink',
            ],
        ];
    }

    public function ssoLink(Service $service): string
    {
        if (!$service->properties()->where('key', 'subscription_id')->exists()) {
            throw new Exception('Missing user organization ID');
        }

        $members = $this->request('/orgs/' . $service->user->properties()->where('key', 'enhance_orgId')->first()->value . '/members', 'get')->json();

        $member = collect($members['items'])->first(function ($item) {
            // Find user where roles contains 'Owner' and loginId is equal to the user's loginId
            return collect($item['roles'])->contains('Owner');
        });

        $data = $this->request('/orgs/' . $service->user->properties()->where('key', 'enhance_orgId')->first()->value . '/members/' . $member['id'] . '/sso', 'get')->json();

        return $data;
    }
}
