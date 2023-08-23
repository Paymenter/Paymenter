<?php

namespace App\Extensions\Servers\DirectAdmin;

use App\Classes\Extensions\Server;
use App\Models\Product;
use App\Helpers\ExtensionHelper;
use App\Extensions\Servers\DirectAdmin\DAHTTPSocket;

class DirectAdmin extends Server
{
    public function createServer($user, $params, $order, $product, $configurableOptions)
    {
        $host = ExtensionHelper::getConfig('DirectAdmin', 'host');
        $sock = new DAHTTPSocket();
        if (ExtensionHelper::getConfig('DirectAdmin', 'ssl')) {
            $sock->connect('ssl://' . $host, '2222');
        } else {
            $sock->connect($host, '2222');
        }
        $server_login = ExtensionHelper::getConfig('DirectAdmin', 'username');
        $server_pass = ExtensionHelper::getConfig('DirectAdmin', 'password');
        $sock->set_login($server_login, $server_pass);
        // Generate random username with 8 characters
        $password = substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz', ceil(10/ strlen($x)))), 1, 10);
        $username = substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz', ceil(8 / strlen($x)))), 1, 8);

        if (isset($params['ip'])) {
            $ip = $params['ip'];
        } else {
            $sock->query('/CMD_API_SHOW_RESELLER_IPS');
            $result = $sock->fetch_parsed_body();
            $ip = $result['list'][0];
        }
        $response = $sock->query(
            '/CMD_API_ACCOUNT_USER',
            [
                'action' => 'create',
                'add' => 'Submit',
                'username' => $username,
                'email' => $user->email,
                'passwd' => $password,
                'passwd2' => $password  ,
                'domain' => $params['config']['domain'],
                'package' => $params['package'],
                'ip' => $ip,
                'notify' => 'yes',
            ]
        );
        $result = $sock->fetch_parsed_body();
        if ($result['error'] != '0') {
            ExtensionHelper::error('DirectAdmin', $result);

            return;
        } else {
            ExtensionHelper::setOrderProductConfig('username', $username, $product->id);
        }

        return $response;
    }

    public function suspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $host = ExtensionHelper::getConfig('DirectAdmin', 'host');
        $sock = new DAHTTPSocket();
        if (ExtensionHelper::getConfig('DirectAdmin', 'ssl')) {
            $sock->connect('ssl://' . $host, '2222');
        } else {
            $sock->connect($host, '2222');
        }
        $server_login = ExtensionHelper::getConfig('DirectAdmin', 'username');
        $server_pass = ExtensionHelper::getConfig('DirectAdmin', 'password');
        $sock->set_login($server_login, $server_pass);
        $username = $params['config']['username'];
        $response = $sock->query(
            '/CMD_API_SELECT_USERS',
            [
                'location' => 'CMD_SELECT_USERS',
                'suspend' => 'suspend',
                'select0' => $username,
            ]
        );
        $result = $sock->fetch_parsed_body();
        if ($result['error'] != '0') {
            ExtensionHelper::error('DirectAdmin', $result);

            return;
        }
    }

    public function unsuspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $host = ExtensionHelper::getConfig('DirectAdmin', 'host');
        $sock = new DAHTTPSocket();
        if (ExtensionHelper::getConfig('DirectAdmin', 'ssl')) {
            $sock->connect('ssl://' . $host, '2222');
        } else {
            $sock->connect($host, '2222');
        }
        $server_login = ExtensionHelper::getConfig('DirectAdmin', 'username');
        $server_pass = ExtensionHelper::getConfig('DirectAdmin', 'password');
        $sock->set_login($server_login, $server_pass);
        $username = $params['config']['username'];
        $response = $sock->query(
            '/CMD_API_SELECT_USERS',
            [
                'location' => 'CMD_SELECT_USERS',
                'suspend' => 'unsuspend',
                'select0' => $username,
            ]
        );
        $result = $sock->fetch_parsed_body();
        if ($result['error'] != '0') {
            ExtensionHelper::error('DirectAdmin', $result, true);

            return;
        }

        return $response;
    }

    public function terminateServer($user, $params, $order, $product, $configurableOptions)
    {
        $host = ExtensionHelper::getConfig('DirectAdmin', 'host');
        $sock = new DAHTTPSocket();
        if (ExtensionHelper::getConfig('DirectAdmin', 'ssl')) {
            $sock->connect('ssl://' . $host, '2222');
        } else {
            $sock->connect($host, '2222');
        }
        $server_login = ExtensionHelper::getConfig('DirectAdmin', 'username');
        $server_pass = ExtensionHelper::getConfig('DirectAdmin', 'password');
        $sock->set_login($server_login, $server_pass);
        $username = $params['config']['username'];
        $response = $sock->query(
            '/CMD_API_SELECT_USERS',
            [
                'confirmed' => 'Confirm',
                'delete' => 'yes',
                'select0' => $username,
            ]
        );
        $result = $sock->fetch_parsed_body();
        if ($result['error'] != '0') {
            error_log(print_r($result, true));

            return;
            // TODO: Handle error
        }

        return $response;
    }

    public function getConfig()
    {
        return [
            [
                'name' => 'host',
                'friendlyName' => 'Host',
                'type' => 'text',
                'required' => true,
                'description' => 'The IP address or domain name of the DirectAdmin server',
            ],
            [
                'name' => 'username',
                'friendlyName' => 'Username',
                'type' => 'text',
                'required' => true,
                'description' => 'The username of the DirectAdmin server',
            ],
            [
                'name' => 'password',
                'friendlyName' => 'Password',
                'type' => 'text',
                'required' => true,
                'description' => 'The password of the DirectAdmin server',
            ],
            [
                'name' => 'ssl',
                'friendlyName' => 'SSL',
                'type' => 'boolean',
                'required' => true,
                'description' => 'Whether to use SSL to connect to the DirectAdmin server',
            ],
        ];
    }

    public function getUserConfig(Product $product)
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

    public function getProductConfig($options)
    {
        return [
            [
                'name' => 'package',
                'type' => 'text',
                'friendlyName' => 'Package',
                'required' => true,
            ],
            [
                'name' => 'ip',
                'type' => 'text',
                'friendlyName' => 'IP',
                'required' => false,
            ],
        ];
    }
}
