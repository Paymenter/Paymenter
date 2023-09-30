<?php

namespace App\Extensions\Servers\Plesk;

use App\Classes\Extensions\Server;
use Illuminate\Support\Facades\Http;
use App\Helpers\ExtensionHelper;

class Plesk extends Server
{
    public function getMetadata()
    {
        return [
            'display_name' => 'Plesk',
            'version' => '1.0.0',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }

    private function getApiKey()
    {
        $username = ExtensionHelper::getConfig('Plesk', 'username');
        $password = ExtensionHelper::getConfig('Plesk', 'password');
        $host = ExtensionHelper::getConfig('Plesk', 'host');
        // Use without verify ssl
        $response = Http::withBasicAuth($username, $password)->withoutVerifying()->post($host . '/api/v2/auth/keys');
        $response = json_decode($response->body(), true);
        return $response['key'];
    }

    public function getConfig()
    {
        return [
            [
                'name' => 'host',
                'friendlyName' => 'Host',
                'type' => 'text',
                'required' => true,
                'description' => 'The IP address or domain name of the Plesk server (with http:// or https://)',
            ],
            [
                'name' => 'username',
                'friendlyName' => 'Username',
                'type' => 'text',
                'required' => true,
                'description' => 'The username of the Plesk server',
            ],
            [
                'name' => 'password',
                'friendlyName' => 'Password',
                'type' => 'text',
                'required' => true,
                'description' => 'The password of the Plesk server',
            ]
        ];
    }

    public function getProductConfig($options)
    {
        return [
            [
                'name' => 'plan',
                'type' => 'text',
                'friendlyName' => 'Plan',
                'required' => true,
                'description' => 'The plan name of the wanted service plan',
            ]
        ];
    }

    public function getUserConfig()
    {
        return [
            [
                'name' => 'domain',
                'type' => 'text',
                'friendlyName' => 'Domain',
                'required' => true,
            ],
        ];
    }

    public function createServer($user, $params, $order, $product, $configurableOptions)
    {
        $apiKey = $this->getApiKey();
        $host = ExtensionHelper::getConfig('Plesk', 'host');
        // Check if client already has a server
        $clientCheck = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-API-Key' => $apiKey
        ])->withoutVerifying()->get($host . '/api/v2/clients/' . $user->id);
        $clientCheck = json_decode($clientCheck->body(), true);
        // Remove spaces and special characters from username
        $username = preg_replace('/[^A-Za-z0-9\-]/', '', $user->name);
        $uuid;
        if (isset($clientCheck['code'])) {
            $newClient = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-API-Key' => $apiKey
            ])->withoutVerifying()->post($host . '/api/v2/clients', [
                'username' => $username,
                'email' => $user->email,
                'password' => $params['password'] ?? $user->password,
                'name' => $user->name,
                'login' => $username,
                'type' => 'customer',
                'external_id' => $user->id,
            ]);
            $newClient = json_decode($newClient->body(), true);
            $uuid = $newClient['guid'];
        } else {
            $uuid = $clientCheck['guid'];
        }
        // Lowercase username
        $username = strtolower($username);
        $domain = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-API-Key' => $apiKey
        ])->withoutVerifying()->post($host . '/api/v2/domains', [
            'name' => $params['config']['domain'],
            'external_id' => $order->id,
            'client' => $uuid,
            'hosting_type' => 'virtual',
            'hosting_settings' => [
                'ftp_login' => $username,
                'ftp_password' => $params['password'] ?? $user->password,
            ],
            'plan' => [
                'name' => $params['plan'],
            ]
        ]);
        $domain = json_decode($domain->body(), true);
        return $domain;
    }

    public function suspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $apiKey = $this->getApiKey();
        $host = ExtensionHelper::getConfig('Plesk', 'host');
        $domain = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-API-Key' => $apiKey
        ])->withoutVerifying()->put($host . '/api/v2/domains/' . $this->getDomainID($params['config']['domain']) . '/status', [
            'status' => 'suspended'
        ]);
        $domain = json_decode($domain->body(), true);
        return $domain;
    }

    private function getDomainID($domain)
    {
        $apiKey = $this->getApiKey();
        $host = ExtensionHelper::getConfig('Plesk', 'host');
        $domain = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-API-Key' => $apiKey
        ])->withoutVerifying()->get($host . '/api/v2/domains?name=' . $domain);
        $domain = json_decode($domain->body(), true);
        return $domain[0]['id'];
    }

    public function unsuspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $apiKey = $this->getApiKey();
        $host = ExtensionHelper::getConfig('Plesk', 'host');
        $domain = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-API-Key' => $apiKey
        ])->withoutVerifying()->put($host . '/api/v2/domains/' . $this->getDomainID($params['config']['domain']) . '/status', [
            'status' => 'active'
        ]);
        $domain = json_decode($domain->body(), true);
        return $domain;
    }

    public function terminateServer($user, $params, $order, $product, $configurableOptions)
    {
        $apiKey = $this->getApiKey();
        $host = ExtensionHelper::getConfig('Plesk', 'host');
        $domain = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-API-Key' => $apiKey
        ])->withoutVerifying()->delete($host . '/api/v2/domains/' . $this->getDomainID($params['config']['domain']));
        $domain = json_decode($domain->body(), true);
        return $domain;
    }

    public function getLink($user, $params)
    {
        $host = ExtensionHelper::getConfig('Plesk', 'host');
        return $host . '/smb/web/overview/id/' . $this->getDomainID($params['config']['domain']) . '/type/domain';
    }
}
