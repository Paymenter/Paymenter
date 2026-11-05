<?php

namespace Paymenter\Extensions\Servers\DirectAdmin;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Server;
use App\Models\Service;
use App\Rules\Domain;
use Exception;
use Illuminate\Support\Facades\Http;

#[ExtensionMeta(
    name: 'DirectAdmin',
    description: 'DirectAdmin server extension',
    version: 'builtin',
    author: 'Paymenter',
    url: 'https://paymenter.org/docs/extensions/directadmin',
    icon: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTExIiBoZWlnaHQ9IjExMSIgdmlld2JveD0iMCAwIDExMSAxMTEiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgY2xhc3M9ImhvdmVyOnNjYWxlLTEyMCBmbGV4IGl0ZW1zLWNlbnRlciBqdXN0aWZ5LWNlbnRlciBzaXplLTE4IHJvdW5kZWQteGwgcC00IGJnLVsjMjIyNzJFXSByaW5nLTYgcmluZy1bIzIyMjcyRV0vNDAiPjxwYXRoIGQ9Ik0xNS44MzQ0IDQwLjE3MUwzMS41NDQzIDU1Ljg4MDlMMTUuODM0NCA3MS41OTA4TDI5Ljk3MzMgODUuNzI5N0w2MS4zOTMxIDU0LjMwOTlMMzEuNTQ0MyAyNC40NjExTDE1LjgzNDQgNDAuMTcxWiIgZmlsbD0iIzMxQjdFOSI+PC9wYXRoPjxwYXRoIGQ9Ik01OS44MjI2IDUyLjczODRMMjguNDAyOCA4NC4xNTgyTDMxLjU0NDggODcuMzAwMkw2Mi45NjQ2IDU1Ljg4MDRMNTkuODIyNiA1Mi43Mzg0WiIgZmlsbD0iIzA2ODJCNCI+PC9wYXRoPjxwYXRoIGQ9Ik0zOS4zOTkyIDE2LjYwNjFMNzguNjc0IDU1Ljg4MDlMMzkuMzk5MiA5NS4xNTU2TDUzLjUzODEgMTA5LjI5NEwxMDguNTIzIDU0LjMwOTlMNTUuMTA5MSAwLjg5NjIzNkwzOS4zOTkyIDE2LjYwNjFaIiBmaWxsPSIjMzFCN0U5Ij48L3BhdGg+PHBhdGggZD0iTTEwNi45NTEgNTIuNzM4TDUxLjk2NjggMTA3LjcyM0w1NS4xMDg4IDExMC44NjVMMTEwLjA5MyA1NS44OEwxMDYuOTUxIDUyLjczOFoiIGZpbGw9IiMwNjgyQjQiPjwvcGF0aD48L3N2Zz4=',
)]
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
            return $this->parse($response);
        }

        return $response;
    }

    private function parse($response)
    {
        $body = html_entity_decode($response->body());
        parse_str($body, $parsed);
        if (isset($parsed['list']) && is_array($parsed['list'])) {
            return $parsed['list'];
        }

        return $parsed;
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

    /**
     * Get product config
     *
     * @param  array  $values
     */
    public function getProductConfig($values = []): array
    {
        $upackages = $this->request('/CMD_API_PACKAGES_USER', parse: true);
        // Map to label => value pairs
        $upackages = array_map(function ($package) {
            return ['label' => $package, 'value' => $package];
        }, $upackages);

        try {
            // If you are a reseller you won't have access to reseller packages
            $rpackages = $this->request('/CMD_API_PACKAGES_RESELLER', parse: true);
            // Merge user packages with reseller packages
            $rpackages = array_map(function ($package) {
                return ['label' => $package . ' (reseller)', 'value' => $package];
            }, $rpackages);
        } catch (Exception $e) {
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

    /**
     * Check if currenct configuration is valid
     */
    public function testConfig(): bool|string
    {
        try {
            $this->request('/CMD_API_SHOW_USERS')->body();

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
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
        $password = substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ(!@.$%', ceil(10 / strlen($x)))), 1, 12);
        $username = substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz', ceil(8 / strlen($x)))), 1, 8);
        $settings = array_merge($settings, $properties);

        if (isset($settings['ip'])) {
            $ip = $settings['ip'];
        } else {
            $ip = $this->request('/CMD_API_SHOW_RESELLER_IPS', parse: true)[0] ?? null;

            if (!$ip) {
                throw new Exception('No IP address available for the server');
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
            throw new Exception('Error creating DirectAdmin account: ' . $response['text']);
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

    /**
     * Suspend a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function suspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['directadmin_username'])) {
            throw new Exception('Service has not been created');
        }

        $response = $this->request('/CMD_API_SELECT_USERS', 'post', [
            'location' => 'CMD_SELECT_USERS',
            'suspend' => 'suspend',
            'select0' => $properties['directadmin_username'],
        ], parse: true);

        if ($response['error'] != '0') {
            throw new Exception('Error suspending DirectAdmin account: ' . $response['text']);
        }

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
        if (!isset($properties['directadmin_username'])) {
            throw new Exception('Service has not been created');
        }

        $response = $this->request('/CMD_API_SELECT_USERS', 'post', [
            'location' => 'CMD_SELECT_USERS',
            'suspend' => 'unsuspend',
            'select0' => $properties['directadmin_username'],
        ], parse: true);

        if ($response['error'] != '0') {
            throw new Exception('Error unsuspending DirectAdmin account: ' . $response['text']);
        }

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
        if (!isset($properties['directadmin_username'])) {
            throw new Exception('Service has not been created');
        }

        $response = $this->request('/CMD_API_SELECT_USERS', 'post', [
            'confirmed' => 'Confirm',
            'delete' => 'yes',
            'select0' => $properties['directadmin_username'],
        ], parse: true);

        if ($response['error'] != '0') {
            throw new Exception('Error terminating DirectAdmin account: ' . $response['text']);
        }

        // Delete the properties
        $service->properties()->where('key', 'directadmin_username')->delete();

        return true;
    }

    public function getActions(Service $service, $settings, $properties): array
    {
        if (!isset($properties['directadmin_username'], $properties['directadmin_password'])) {
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
        if (!isset($properties['directadmin_username'], $properties['directadmin_password'])) {
            return '';
        }

        $response = Http::withBasicAuth($properties['directadmin_username'], $properties['directadmin_password'])
            ->post(rtrim($this->config('host'), '/') . '/CMD_API_LOGIN_KEYS', [
                'action' => 'create',
                'type' => 'one_time_url',
                'expiry' => '5m',
            ])->throw();

        $response = $this->parse($response);

        if (!isset($response['error'])) {
            throw new Exception('Unexpected DirectAdmin response while creating SSO link');
        }

        if ($response['error'] != '0') {
            throw new Exception('Error creating DirectAdmin SSO link: ' . ($response['text'] ?? 'Unknown error'));
        }

        return $response['details'] ?? '';
    }
}
