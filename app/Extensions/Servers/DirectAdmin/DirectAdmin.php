<?php

namespace App\Extensions\Servers\DirectAdmin;

use App\Classes\Extensions\Server;
use App\Extensions\Servers\DirectAdmin\DAHTTPSocket;
use App\Helpers\ExtensionHelper;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class DirectAdmin extends Server
{
    public function getMetadata()
    {
        return [
            'display_name' => 'DirectAdmin',
            'version'      => '1.0.1',
            'author'       => 'Paymenter',
            'website'      => 'https://paymenter.org',
        ];
    }

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
        $server_pass  = ExtensionHelper::getConfig('DirectAdmin', 'password');
        $sock->set_login($server_login, $server_pass);
        // Generate random username with 8 characters
        $password = substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ(!@.$%', ceil(10 / strlen($x)))), 1, 12);
        $username = substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz', ceil(8 / strlen($x)))), 1, 8);

        if (isset($params['ip'])) {
            $ip = $params['ip'];
        } else {
            $sock->query('/CMD_API_SHOW_RESELLER_IPS');
            $result = $sock->fetch_parsed_body();
            $ip     = $result['list'][0];
        }
        $response = $sock->query(
            '/CMD_API_ACCOUNT_USER',
            [
                'action'   => 'create',
                'add'      => 'Submit',
                'username' => $username,
                'email'    => $user->email,
                'passwd'   => $password,
                'passwd2'  => $password,
                'domain'   => $params['config']['domain'],
                'package'  => $params['package'],
                'ip'       => $ip,
                'notify'   => 'yes',
            ]
        );
        $result = $sock->fetch_parsed_body();
        if ($result['error'] != '0') {
            ExtensionHelper::error('DirectAdmin', $result);

            return;
        } else {
            ExtensionHelper::setOrderProductConfig('cpurl', (ExtensionHelper::getConfig('DirectAdmin', 'ssl') ? "https://" : "http://") . $host . ":2222", $product->id);
            ExtensionHelper::setOrderProductConfig('username', $username, $product->id);
            ExtensionHelper::setOrderProductConfig('password', $password, $product->id);

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
        $server_pass  = ExtensionHelper::getConfig('DirectAdmin', 'password');
        $sock->set_login($server_login, $server_pass);
        $username = $params['config']['username'];
        $response = $sock->query(
            '/CMD_API_SELECT_USERS',
            [
                'location' => 'CMD_SELECT_USERS',
                'suspend'  => 'suspend',
                'select0'  => $username,
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
        $server_pass  = ExtensionHelper::getConfig('DirectAdmin', 'password');
        $sock->set_login($server_login, $server_pass);
        $username = $params['config']['username'];
        $response = $sock->query(
            '/CMD_API_SELECT_USERS',
            [
                'location' => 'CMD_SELECT_USERS',
                'suspend'  => 'unsuspend',
                'select0'  => $username,
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
        $server_pass  = ExtensionHelper::getConfig('DirectAdmin', 'password');
        $sock->set_login($server_login, $server_pass);
        $username = $params['config']['username'];
        $response = $sock->query(
            '/CMD_API_SELECT_USERS',
            [
                'confirmed' => 'Confirm',
                'delete'    => 'yes',
                'select0'   => $username,
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
                'name'         => 'host',
                'friendlyName' => 'Host',
                'type'         => 'text',
                'required'     => true,
                'description'  => 'The IP address or domain name of the DirectAdmin server',
            ],
            [
                'name'         => 'username',
                'friendlyName' => 'Username',
                'type'         => 'text',
                'required'     => true,
                'description'  => 'The username of the DirectAdmin server',
            ],
            [
                'name'         => 'password',
                'friendlyName' => 'Password',
                'type'         => 'text',
                'required'     => true,
                'description'  => 'The password of the DirectAdmin server',
            ],
            [
                'name'         => 'ssl',
                'friendlyName' => 'SSL',
                'type'         => 'boolean',
                'required'     => true,
                'description'  => 'Whether to use SSL to connect to the DirectAdmin server',
            ],
        ];
    }

    public function getUserConfig(Product $product)
    {
        return [
            [
                'name'         => 'domain',
                'type'         => 'text',
                'friendlyName' => 'Domain',
                'required'     => true,
            ],
        ];
    }

    public function getProductConfig($options)
    {
        // Get package options
        $packages = [];
        $host     = ExtensionHelper::getConfig('DirectAdmin', 'host');
        $pass     = ExtensionHelper::getConfig('DirectAdmin', 'password');
        $user     = ExtensionHelper::getConfig('DirectAdmin', 'username');
        $ssl      = ExtensionHelper::getConfig('DirectAdmin', 'ssl');
        $sock     = new DAHTTPSocket();
        if ($ssl) {
            $sock->connect('ssl://' . $host, '2222');
        } else {
            $sock->connect($host, '2222');
        }
        $sock->set_login($user, $pass);
        $sock->query('/CMD_API_PACKAGES_USER');
        $result = $sock->fetch_parsed_body();
        if (!isset($result['list'])) {
            throw new \Exception('No packages found or could not connect to DirectAdmin server');
        }

        foreach ($result['list'] as $package) {
            $packages[] = [
                'name'  => $package,
                'value' => $package,
            ];
        }

        $ips = [];
        $sock->query('/CMD_API_SHOW_RESELLER_IPS');
        $result = $sock->fetch_parsed_body();
        foreach ($result['list'] as $ip) {
            $ips[] = [
                'name'  => $ip,
                'value' => $ip,
            ];
        }

        return [
            [
                'name'         => 'package',
                'type'         => 'dropdown',
                'friendlyName' => 'Package',
                'required'     => true,
                'options'      => $packages,
            ],
            [
                'name'         => 'ip',
                'type'         => 'dropdown',
                'friendlyName' => 'IP',
                'required'     => true,
                'options'      => $ips,
            ],
        ];
    }

    public function getCustomPages($user, $params, $order, $product2, $configurableOptions)
    {

        $host = ExtensionHelper::getConfig('DirectAdmin', 'host');
        $sock = new DAHTTPSocket();
        if (ExtensionHelper::getConfig('DirectAdmin', 'ssl')) {
            $sock->connect('ssl://' . $host, '2222');
        } else {
            $sock->connect($host, '2222');
        }
        $server_login = ExtensionHelper::getConfig('DirectAdmin', 'username');
        $server_pass  = ExtensionHelper::getConfig('DirectAdmin', 'password');
        $sock->set_login($server_login, $server_pass);
        $username = $params['config']['username'];
        $response = $sock->query(
            '/CMD_API_SHOW_USER_USAGE',
            [
                'user' => $username,
            ]
        );
        $stats = $sock->fetch_parsed_body();

        $response = $sock->query(
            '/CMD_API_SHOW_USER_CONFIG',
            [
                'user' => $username,
            ]
        );
        $limit = $sock->fetch_parsed_body();

        return [
            'name'     => 'DirectAdmin',
            'template' => 'directadmin::control',
            'data'     => [
                'stats' => $stats,
                'limit' => $limit,
                'user'  => [
                    'panel'    => (ExtensionHelper::getConfig('DirectAdmin', 'ssl') ? "https://" : "http://") . $host . ":2222",
                    'username' => $params['config']['username'],
                    'password' => $params['config']['password'],
                    'domain'   => $params['config']['domain'],
                ],
            ],
            'pages'    => [
            ],
        ];
    }

    public function login(Request $request, OrderProduct $product)
    {
        if (!ExtensionHelper::hasAccess($product, $request->user())) {
            throw new Exception('You do not have access to this product');
        }

        $data   = ExtensionHelper::getParameters($product);
        $params = $data->config;

        $host = ExtensionHelper::getConfig('DirectAdmin', 'host');
        $sock = new DAHTTPSocket();
        if (ExtensionHelper::getConfig('DirectAdmin', 'ssl')) {
            $sock->connect('ssl://' . $host, '2222');
        } else {
            $sock->connect($host, '2222');
        }

        $server_login = $params['config']['username'];
        $server_pass  = $params['config']['password'];
        $sock->set_login($server_login, $server_pass);

        $response = $sock->query(
            '/CMD_API_LOGIN_KEYS',
            [
                'action'                        => 'create',
                'type'                          => 'one_time_url',
                'redirect-url'                  => (ExtensionHelper::getConfig('DirectAdmin', 'ssl') ? "https://" : "http://") . $host . ":2222",
                'login_keys_notify_on_creation' => 0,
                'expiry'                        => '5m',
                'user'                          => $server_login,
                'passwd'                        => $server_pass,
            ]
        );
        $result = $sock->fetch_parsed_body();
        if (!isset($result['error']) || $result['error'] != 0) {
            return response()->json([
                'status'  => 'error',
                'message' => $result['text'] ?? "Unable to create login link.",
                'data'    => new \stdClass(),
            ]);
        }
        return response()->json([
            'status'  => 'success',
            'message' => $result['text'],
            'data'    => [
                'url' => $result['details'],
            ],
        ]);
    }

    public function resetPwd(Request $request, OrderProduct $product)
    {
        if (!ExtensionHelper::hasAccess($product, $request->user())) {
            throw new Exception('You do not have access to this product');
        }

        $data   = ExtensionHelper::getParameters($product);
        $params = $data->config;

        $host = ExtensionHelper::getConfig('DirectAdmin', 'host');
        $sock = new DAHTTPSocket();
        if (ExtensionHelper::getConfig('DirectAdmin', 'ssl')) {
            $sock->connect('ssl://' . $host, '2222');
        } else {
            $sock->connect($host, '2222');
        }
        $server_login = ExtensionHelper::getConfig('DirectAdmin', 'username');
        $server_pass  = ExtensionHelper::getConfig('DirectAdmin', 'password');
        $sock->set_login($server_login, $server_pass);
        $sock->set_method("POST");
        $username = $params['config']['username'];
        $password = substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ(!@.$%', ceil(10 / strlen($x)))), 1, 12);

        $response = $sock->query(
            '/CMD_API_USER_PASSWD',
            [
                'username' => $username,
                'passwd'   => $password,
                'passwd2'  => $password,
                'options'  => 'yes',
                'system'   => 'yes',
                'ftp'      => 'yes',
                'database' => 'yes',
            ]
        );

        $result = $sock->fetch_parsed_body();

        if (!isset($result['error']) || $result['error'] != 0) {
            return response()->json([
                'status'  => 'error',
                'message' => $result['text'] ?? "Unable to change password.",
                'data'    => $result,
            ]);
        }

        ExtensionHelper::setOrderProductConfig('password', $password, $product->id);

        return response()->json([
            'status'  => 'success',
            'message' => "Password has been reset.",
            'data'    => new \stdClass(),
        ]);

    }

}
