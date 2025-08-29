<?php

namespace Paymenter\Extensions\Servers\HestiaCP;

use App\Classes\Extension\Server;
use App\Models\Service;
use App\Rules\Domain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class HestiaCP extends Server
{
    private function request($data = [])
    {
        $host = rtrim($this->config('host'), '/');
        $port = rtrim($this->config('port'), '/');
        $accesskey = $this->config('accesskey');
        $secretkey = $this->config('secretkey');

        $data['hash'] = $accesskey . ':' . $secretkey;
        $response = Http::post($host . ':' . $port . '/api/', $data);
        if (!$response->successful()) {
            dd($response->body(), $response->status());
            throw new \Exception('Error while requesting API');
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
            ],
            [
                'name' => 'port',
                'type' => 'text',
                'label' => 'Port',
                'validation' => 'numeric',
                'required' => true,
            ],
            [
                'name' => 'accesskey',
                'type' => 'text',
                'label' => 'Access Key ID',
                'required' => true,
            ],
            [
                'name' => 'secretkey',
                'type' => 'text',
                'label' => 'Secret key',
                'required' => true,
            ],
        ];
    }

    public function getProductConfig($options)
    {
        // Get all the packages
        $response = $this->request(['cmd' => 'v-list-user-packages', 'arg1' => 'json']);
        $packages = $response->json();
        $packageOptions = [];
        foreach ($packages as $package => $options) {
            $packageOptions[$package] = $package;
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

    public function getCheckoutConfig()
    {
        return [
            [
                'name' => 'domain',
                'type' => 'text',
                'validation' => [new Domain, 'required'],
                'label' => 'Domain',
                'placeholder' => 'domain.com',
                'required' => true,
            ]
        ];
    }

    /**
     * Check if current configuration is valid
     */
    public function testConfig(): bool|string
    {
        try {
            $this->request([
                'cmd' => 'v-list-users',
            ]);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Generate a random string, using a cryptographically secure 
     * pseudorandom number generator (random_int)
     * 
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     * 
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     */
    private static function random_str(
        $length,
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ) {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        if ($max < 1) {
            throw new \Exception('$keyspace must be at least two characters long');
        }
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    public function createServer(Service $service, $settings, $properties)
    {
        $original_username = explode('@', $service->user->email)[0];
        $username = $original_username . self::random_str(6, '0123456789');
        $password = self::random_str(16);

        // If first one is a number, add a letter
        if (is_numeric($username[0])) {
            $username = 'a' . substr($username, 1);
        }

        $this->request([
            'cmd' => 'v-add-user',
            'arg1' => $username,
            'arg2' => $password,
            'arg3' => $service->user->email,
            'arg4' => $settings['package'],
            'arg5' => $original_username,
        ]);

        $this->request([
            'cmd' => 'v-add-domain',
            'arg1' => $username,
            'arg2' => $properties['domain'],
        ]);

        $service->properties()->updateOrCreate([
            'key' => 'hestiacp_username',
        ], [
            'name' => 'HestiaCP Username',
            'value' => strtolower($username),
        ]);

        $service->properties()->updateOrCreate([
            'key' => 'hestiacp_password',
        ], [
            'name' => 'HestiaCP Password',
            'value' => $password,
        ]);


        return true;
    }

    public function upgradeServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['hestiacp_username']) || !isset($properties['hestiacp_password'])) {
            throw new \Exception("Service has not been created");
        }

        $this->request([
            'cmd' => 'v-change-user-package',
            'arg1' => $properties['hestiacp_username'],
            'arg2' => $settings['package'],
        ]);

        return true;
    }

    public function suspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['hestiacp_username']) || !isset($properties['hestiacp_password'])) {
            throw new \Exception("Service has not been created");
        }

        $this->request([
            'cmd' => 'v-suspend-user',
            'arg1' => $properties['hestiacp_username'],
        ]);

        return true;
    }

    public function unsuspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['hestiacp_username']) || !isset($properties['hestiacp_password'])) {
            throw new \Exception("Service has not been created");
        }

        $this->request([
            'cmd' => 'v-unsuspend-user',
            'arg1' => $properties['hestiacp_username'],
        ]);

        return true;
    }

    public function terminateServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['hestiacp_username']) || !isset($properties['hestiacp_password'])) {
            throw new \Exception("Service has not been created");
        }

        $this->request([
            'cmd' => 'v-delete-user',
            'arg1' => $properties['hestiacp_username'],
        ]);

        $service->properties()->where('key', 'hestiacp_username')->delete();
        $service->properties()->where('key', 'hestiacp_password')->delete();

        return true;
    }

    public function getActions(Service $service, $settings, $properties): array
    {
        if (!isset($properties['hestiacp_username']) || !isset($properties['hestiacp_password'])) {
            return [];
        }

        return [
            [
                'type' => 'button',
                'url' => $this->config('host') . ':' . $this->config('port'),
                'label' => 'Go to Control Panel',
            ],
            [
                'type' => 'text',
                'text' => $properties['hestiacp_username'],
                'label' => 'HestiaCP Username',
            ],
            [
                'type' => 'text',
                'text' => $properties['hestiacp_password'],
                'label' => 'HestiaCP Password',
            ]
        ];
    }
}
