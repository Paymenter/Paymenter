<?php

namespace Paymenter\Extensions\Servers\DirectAdmin;

use App\Classes\Extension\Server;
use App\Models\Service;
use App\Rules\Domain;
use Illuminate\Support\Facades\Http;

class DirectAdmin extends Server
{
    private function request($endpoint, $method = 'get', $data = [], $parse = false)
    {
        $host = rtrim($this->config('host'), '/');
        $url = $host . $endpoint;

        $response = Http::withBasicAuth(
            $this->config('username'),
            $this->config('password')
        )->withHeaders([
            'Content-Type' => 'application/json',
        ])->$method($url, $data)->throw();

        if ($parse) {
            $body = html_entity_decode($response->body());
            parse_str($body, $parsed);
            if (isset($parsed['list']) && is_array($parsed['list'])) {
                return $parsed['list'];
            }

            return $parsed;
        }

        return $response;
    }

    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'host',
                'label' => 'DirectAdmin URL',
                'type' => 'text',
                'required' => true,
                'validation' => 'url',
            ],
            [
                'name' => 'username',
                'label' => 'DirectAdmin User name',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'password',
                'label' => 'DirectAdmin Password',
                'type' => 'password',
                'required' => true,
                'encrypted' => true,
            ],
        ];
    }

    public function getProductConfig($values = []): array
    {
        $upackages = $this->request('/CMD_API_PACKAGES_USER', parse: true);
        $upackages = array_map(function ($package) {
            return ['label' => $package, 'value' => $package];
        }, $upackages);

        try {
            $rpackages = $this->request('/CMD_API_PACKAGES_RESELLER', parse: true);
            $rpackages = array_map(function ($package) {
                return ['label' => $package . ' (reseller)', 'value' => $package];
            }, $rpackages);
        } catch (\Exception $e) {
            $rpackages = [];
        }

        $packages = array_merge($upackages, $rpackages);
        $ips = $this->request('/CMD_API_SHOW_RESELLER_IPS', parse: true);

        return [
            [
                'name' => 'package',
                'type' => 'select',
                'label' => 'Package',
                'required' => true,
                'options' => $packages,
            ],
            [
                'name' => 'ip',
                'type' => 'select',
                'label' => 'IP Address',
                'required' => false,
                'options' => $ips,
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

    public function testConfig(): bool|string
    {
        try {
            $this->request('/CMD_API_SHOW_USERS')->body();
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function createServer(Service $service, $settings, $properties)
    {
        $password = substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ(!@.$%', ceil(10 / strlen($x)))), 1, 12);
        $username = substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz', ceil(8 / strlen($x)))), 1, 8);
        $settings = array_merge($settings, $properties);

        if (isset($settings['ip'])) {
            $ip = $settings['ip'];
        } else {
            $ip = $this->request('/CMD_API_SHOW_RESELLER_IPS', parse: true)[0] ?? null;
            if (!$ip) {
                throw new \Exception('No IP address available for the server');
            }
        }

        $response = $this->request('/CMD_API_ACCOUNT_USER', 'post', [
            'action' => 'create',
            'add' => 'Submit',
            'username' => $username,
            'email' => $service->user->email,
            'passwd' => $password,
            'passwd2' => $password,
            'package' => $settings['package'],
            'ip' => $ip,
            'domain' => $properties['domain'] ?? '',
            'notify' => 'yes',
        ], parse: true);

        if ($response['error'] != '0') {
            throw new \Exception('Error creating DirectAdmin account: ' . $response['text']);
        }

        $service->properties()->updateOrCreate([
            'key' => 'directadmin_username',
        ], [
            'name' => 'DirectAdmin username',
            'value' => $username,
        ]);

        $service->properties()->updateOrCreate([
            'key' => 'directadmin_password',
        ], [
            'name' => 'DirectAdmin password',
            'value' => $password,
        ]);

        return [
            'username' => $username,
            'password' => $password,
            'domain' => $properties['domain'] ?? '',
            'ip' => $ip,
        ];
    }

    public function suspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['directadmin_username'])) {
            throw new \Exception('Service has not been created');
        }

        $response = $this->request('/CMD_API_SELECT_USERS', 'post', [
            'location' => 'CMD_SELECT_USERS',
            'suspend' => 'suspend',
            'select0' => $properties['directadmin_username'],
        ], parse: true);

        if ($response['error'] != '0') {
            throw new \Exception('Error suspending DirectAdmin account: ' . $response['text']);
        }

        return true;
    }

    public function unsuspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['directadmin_username'])) {
            throw new \Exception('Service has not been created');
        }

        $response = $this->request('/CMD_API_SELECT_USERS', 'post', [
            'location' => 'CMD_SELECT_USERS',
            'suspend' => 'unsuspend',
            'select0' => $properties['directadmin_username'],
        ], parse: true);

        if ($response['error'] != '0') {
            throw new \Exception('Error unsuspending DirectAdmin account: ' . $response['text']);
        }

        return true;
    }

    public function terminateServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['directadmin_username'])) {
            throw new \Exception('Service has not been created');
        }

        $response = $this->request('/CMD_API_SELECT_USERS', 'post', [
            'confirmed' => 'Confirm',
            'delete' => 'yes',
            'select0' => $properties['directadmin_username'],
        ], parse: true);

        if ($response['error'] != '0') {
            throw new \Exception('Error terminating DirectAdmin account: ' . $response['text']);
        }

        $service->properties()->where('key', 'directadmin_username')->delete();

        return true;
    }

    public function getActions(Service $service, $settings, $properties): array
    {
        if (!isset($properties['directadmin_username'])) {
            return [];
        }

        return [
            [
                'label' => 'Access DirectAdmin',
                'type' => 'button',
                'function' => 'ssoLink',
            ],
        ];
    }

    public function ssoLink(Service $service, $settings, $properties): string
    {
        if (!isset($properties['directadmin_username']) || !isset($properties['directadmin_password'])) {
            return '';
        }

        // FIX: Previously the SSO link was generated using the DirectAdmin admin account credentials from the extension config,
        // causing the link to log into the admin account instead of the user's account.
        // This now uses the user's DirectAdmin credentials to generate the one-time URL.
        $response = Http::withBasicAuth(
            $properties['directadmin_username'],
            $properties['directadmin_password']
        )->withHeaders([
            'Content-Type' => 'application/json',
        ])->post(rtrim($this->config('host'), '/') . '/CMD_API_LOGIN_KEYS', [
            'action' => 'create',
            'type' => 'one_time_url',
            'expiry' => '5m',
        ])->throw();

        $body = html_entity_decode($response->body());
        parse_str($body, $parsed);

        if (!isset($parsed['error']) || $parsed['error'] != '0') {
            throw new \Exception('Error creating DirectAdmin SSO link: ' . ($parsed['text'] ?? 'Unknown error'));
        }

        return $parsed['details'] ?? '';
    }
}
