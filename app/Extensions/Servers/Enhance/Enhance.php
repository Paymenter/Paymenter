<?php

namespace App\Extensions\Servers\Enhance;

use App\Classes\Extensions\Server;
use App\Helpers\ExtensionHelper;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class Enhance extends Server
{
    private $host;
    private $apikey;

    /**
     * Get metadata
     * 
     * @return array
     */
    public function getMetadata()
    {
        return [
            'display_name' => 'Enhance',
            'version' => '1.0.0',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }

    public function __construct($extension)
    {
        parent::__construct($extension);
        $this->host = ExtensionHelper::getConfig('Enhance', 'host');
        $this->apikey = ExtensionHelper::getConfig('Enhance', 'apikey');
    }


    private function request($method, $endpoint, $data = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apikey,
        ])->$method($this->host . '/api' . $endpoint, $data);
        if ($response->failed()) {
            throw new \Exception('Error while requesting API');
        }
        return $response;
    }

    /**
     * Get all the configuration for the extension
     * 
     * @return array
     */
    public function getConfig()
    {
        return [
            [
                'name' => 'host',
                'type' => 'text',
                'friendlyName' => 'Hostname',
                'validation' => 'url:http,https',
                'required' => true,
            ],
            [
                'name' => 'apikey',
                'type' => 'text',
                'friendlyName' => 'API key',
                'required' => true,
            ],
            [
                'name' => 'orgId',
                'type' => 'text',
                'friendlyName' => 'Organization ID',
                'required' => true,
            ]
        ];
    }

    /**
     * Get product config
     * 
     * @param array $options
     * @return array
     */
    public function getProductConfig($options)
    {
        $plans = $this->request('get', '/orgs/' . ExtensionHelper::getConfig('Enhance', 'orgId') . '/plans')->json();
        $plans = array_map(function ($plan) {
            return [
                'value' => $plan['id'],
                'name' => $plan['name'],
            ];
        }, $plans['items']);

        return [
            [
                'name' => 'plan',
                'type' => 'dropdown',
                'friendlyName' => 'Plan',
                'options' => $plans,
                'required' => true,
            ],
        ];
    }

    /**
     * Get user config
     * 
     * @param array $options
     * @return array
     */
    public function getUserConfig($options)
    {
        return [
            [
                'name' => 'domain',
                'type' => 'text',
                'friendlyName' => 'Domain',
                'required' => true,
                'validation' => 'domain',
            ],
        ];
    }


    /**
     * Create a server
     * 
     * @param User $user
     * @param array $params
     * @param Order $order
     * @param OrderProduct $orderProduct
     * @param array $configurableOptions
     * @return bool
     */
    public function createServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        // Check if customer exists
        $cresponse = $this->request('get', '/orgs/' . ExtensionHelper::getConfig('Enhance', 'orgId') . '/customers?email=' . $user->email)->json();
        foreach ($cresponse['items'] as $customer) {
            if (!isset($customer['ownerEmail'])) continue;
            if ($customer['ownerEmail'] == $user->email) {
                $orgUUID = $customer['id'];
                $loginUUID = $customer['ownerId'];
                break;
            }
        }
        if (!isset($orgUUID)) {
            $cresponse = $this->request('post', '/orgs/' . ExtensionHelper::getConfig('Enhance', 'orgId') . '/customers', [
                'name' => $user->name,
            ])->json();
            $orgUUID = $cresponse['id'];
        }
        // Check if website exists
        $domainExists = $this->request('get', '/orgs/' . $orgUUID . '/websites?search=' . $params['config']['domain'])->json();
        if ($domainExists['total'] > 0) {
            throw new \Exception('Domain already exists');
        }


        $sresponse = $this->request('post', '/orgs/' . ExtensionHelper::getConfig('Enhance', 'orgId') . '/customers/' . $orgUUID . '/subscriptions', [
            'planId' => (int) $params['plan'],
        ])->json();
        // CHeck if login exists
        $response = $this->request('get', '/logins')->json();

        foreach ($response['items'] as $login) {
            if ($login['email'] == $user->email) {
                $loginUUID = $login['id'];
                break;
            }
        }

        if (!isset($loginUUID)) {
            $randomPassword = Str::password();

            $lresponse = $this->request('post', '/logins?orgId=' . $orgUUID, [
                'email' => $user->email,
                'password' => $randomPassword,
                'name' => $user->name,
            ])->json();

            $loginUUID = $lresponse['id'];

            $this->request('post', '/orgs/' . $orgUUID . '/members', [
                'loginId' => $loginUUID,
                'roles' => ['Owner'],
            ])->json();
        }

        $response = $this->request('post', '/orgs/' . $orgUUID . '/websites', [
            'domain' => $params['config']['domain'],
            'subscriptionId' => $sresponse['id'],
        ])->json();

        return true;
    }

    /**
     * Suspend a server
     * 
     * @param User $user
     * @param array $params
     * @param Order $order
     * @param OrderProduct $orderProduct
     * @param array $configurableOptions
     * @return bool
     */
    public function suspendServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        $cresponse = $this->request('get', '/orgs/' . ExtensionHelper::getConfig('Enhance', 'orgId') . '/customers?email=' . $user->email)->json();
        foreach ($cresponse['items'] as $customer) {
            if (!isset($customer['ownerEmail'])) continue;
            if ($customer['ownerEmail'] == $user->email) {
                $orgUUID = $customer['id'];
                break;
            }
        }
        if (!isset($orgUUID)) {
            $cresponse = $this->request('post', '/orgs/' . ExtensionHelper::getConfig('Enhance', 'orgId') . '/customers', [
                'name' => $user->name,
            ])->json();
            $orgUUID = $cresponse['id'];
        }

        $response = $this->request('get', '/orgs/' . $orgUUID . '/websites?search=' . $params['config']['domain'])->json();
        $websiteUUID = $response['items'][0]['id'];

        $this->request('patch', '/orgs/' . $orgUUID . '/websites/' . $websiteUUID, [
            'isSuspended' => true,
        ])->json();

        return false;
    }

    /**
     * Unsuspend a server
     * 
     * @param User $user
     * @param array $params
     * @param Order $order
     * @param OrderProduct $orderProduct
     * @param array $configurableOptions
     * @return bool
     */
    public function unsuspendServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        $cresponse = $this->request('get', '/orgs/' . ExtensionHelper::getConfig('Enhance', 'orgId') . '/customers?email=' . $user->email)->json();
        foreach ($cresponse['items'] as $customer) {
            if (!isset($customer['ownerEmail'])) continue;
            if ($customer['ownerEmail'] == $user->email) {
                $orgUUID = $customer['id'];
                break;
            }
        }
        if (!isset($orgUUID)) {
            $cresponse = $this->request('post', '/orgs/' . ExtensionHelper::getConfig('Enhance', 'orgId') . '/customers', [
                'name' => $user->name,
            ])->json();
            $orgUUID = $cresponse['id'];
        }

        $response = $this->request('get', '/orgs/' . $orgUUID . '/websites?search=' . $params['config']['domain'])->json();
        $websiteUUID = $response['items'][0]['id'];

        $this->request('patch', '/orgs/' . $orgUUID . '/websites/' . $websiteUUID, [
            'isSuspended' => false,
        ])->json();
        
        return false;
    }

    /**
     * Terminate a server
     * 
     * @param User $user
     * @param array $params
     * @param Order $order
     * @param OrderProduct $orderProduct
     * @param array $configurableOptions
     * @return bool
     */
    public function terminateServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        $cresponse = $this->request('get', '/orgs/' . ExtensionHelper::getConfig('Enhance', 'orgId') . '/customers?email=' . $user->email)->json();
        foreach ($cresponse['items'] as $customer) {
            if (!isset($customer['ownerEmail'])) continue;
            if ($customer['ownerEmail'] == $user->email) {
                $orgUUID = $customer['id'];
                $loginUUID = $customer['ownerId'];
                break;
            }
        }
        if (!isset($orgUUID)) {
            $cresponse = $this->request('post', '/orgs/' . ExtensionHelper::getConfig('Enhance', 'orgId') . '/customers', [
                'name' => $user->name,
            ])->json();
            $orgUUID = $cresponse['id'];
        }

        $response = $this->request('get', '/orgs/' . $orgUUID . '/websites?search=' . $params['config']['domain'])->json();
        $website = $response['items'][0];

        
        
        // Delete subscription
        $subscriptionUUID = $website['subscriptionId'];
        $this->request('delete', '/orgs/' . $orgUUID . '/subscriptions/' . $subscriptionUUID)->json();
        $this->request('delete', '/orgs/' . $orgUUID . '/websites/' . $website['id'])->json();


        return false;
    }
}
