<?php

namespace App\Extensions\Servers\Goedge;

use App\Classes\Extensions\Server;
use App\Helpers\ExtensionHelper;
use App\Models\Product;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Cache;

class Goedge extends Server
{
    /**
     * Get the extension metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return [
            'display_name' => 'GoEdge CDN',
            'version'      => '1.0.0',
            'author'       => 'Shira Kagurazaka',
            'website'      => 'https://blog.ni-co.moe',
        ];
    }

    public function getConfig()
    {
        return [
            [
                'name'         => 'endpoint',
                'friendlyName' => 'API Endpoint',
                'type'         => 'text',
                'required'     => true,
                'description'  => 'Your GoEdge API endpoint (with http:// or https://)',
            ],
            [
                'name'         => 'access_id',
                'friendlyName' => 'Access Key ID',
                'type'         => 'text',
                'required'     => true,
                'description'  => 'Your GoEdge API Access Key ID (must be admin role)',
            ],
            [
                'name'         => 'access_key',
                'friendlyName' => 'Access Key',
                'type'         => 'text',
                'required'     => true,
                'description'  => 'Your GoEdge API Access Key (must be admin role)',
            ],
            [
                'name'         => 'node_cluster_id',
                'friendlyName' => 'Cluster ID',
                'type'         => 'text',
                'required'     => true,
                'description'  => 'Users created will be assigned to this cluster',
            ],
            [
                'name'         => 'user_panel',
                'friendlyName' => 'User Panel Url',
                'type'         => 'text',
                'required'     => true,
                'description'  => 'Users created will be assigned to this cluster',
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

        $result = $this->fetchPost('/PlanService/findAllAvailablePlans', []);

        if (!isset($result['data']['plans'])) {
            ExtensionHelper::error('Goedge', "Unable to get the list of available packages or the list is empty.");
            return [
                [
                    'name'         => 'package',
                    'type'         => 'dropdown',
                    'friendlyName' => 'Package',
                    'required'     => true,
                    'options'      => [],
                ],
                [
                    'name'         => "period",
                    'type'         => 'dropdown',
                    'friendlyName' => 'Period Type',
                    'required'     => true,
                    'options'      => [
                        [
                            'name'  => 'Monthly',
                            'value' => 'monthly',
                        ],
                        [
                            'name'  => "Seasonally",
                            'value' => 'seasonally',
                        ],
                        [
                            'name'  => 'Yearly',
                            'value' => 'yearly',
                        ],
                    ],
                ],
            ];
            // throw new \Exception("Unable to get the list of available packages or the list is empty.", 0);
        }

        $packages = [];

        foreach ($result['data']['plans'] as $plan) {
            $packages[] = [
                'name'  => $plan['name'],
                'value' => $plan['id'],
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
                'name'         => "period",
                'type'         => 'dropdown',
                'friendlyName' => 'Period Type',
                'required'     => true,
                'options'      => [
                    [
                        'name'  => 'Monthly',
                        'value' => 'monthly',
                    ],
                    [
                        'name'  => "Seasonally",
                        'value' => 'seasonally',
                    ],
                    [
                        'name'  => 'Yearly',
                        'value' => 'yearly',
                    ],
                ],
            ],
        ];

    }

    private function fetchPost(string $api, array $data): array
    {
        $endpoint = ExtensionHelper::getConfig('Goedge', 'endpoint');
        $token    = $this->_getGoedgeToken();
        // var_dump($token);exit;
        $client = new Client();

        $request_param = [
            'headers' => [
                'Content-Type'        => 'application/json',
                'X-Edge-Access-Token' => $token,
            ],
        ];

        if (!empty($data)) {
            $request_param[RequestOptions::JSON] = $data;
        }

        try {
            $response = $client->post($endpoint . $api, $request_param);

            $responseBody = (string) $response->getBody();

            $result = json_decode($responseBody, true);
            if (empty($result) || $result['code'] !== 200) {
                ExtensionHelper::error('Goedge', $result['message'] ?? "no error message.");
                return [];
            }

            return $result;

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            ExtensionHelper::error('Goedge', "Request exception from {$api}, error:{$e->getMessage()}");
            return [];
        } catch (\Exception $e) {
            ExtensionHelper::error('Goedge', "Unable fetch data from {$api}, error:{$e->getMessage()}");
            return [];
        }
    }

    private function _getGoedgeToken()
    {
        $endpoint   = ExtensionHelper::getConfig('Goedge', 'endpoint');
        $access_id  = ExtensionHelper::getConfig('Goedge', 'access_id');
        $access_key = ExtensionHelper::getConfig('Goedge', 'access_key');

        $cache_key = "goedge_token_" . crc32("{$endpoint}|{$access_id}|{$access_key}");
        if (Cache::has($cache_key)) {
            return Cache::get($cache_key);
        }

        $client = new Client();

        try {
            $response = $client->post($endpoint . '/APIAccessTokenService/getAPIAccessToken', [
                RequestOptions::JSON => [
                    'type'        => "admin",
                    'accessKeyId' => $access_id,
                    'accessKey'   => $access_key,
                ],
                'headers'            => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $responseBody = (string) $response->getBody();

            $result = json_decode($responseBody, true);
            if (empty($result) || $result['code'] !== 200) {
                throw new \Exception($result['message'] ?? "No token was obtained.", $result['code'] ?? 0);
            }
            $token  = $result['data']['token'];
            $exp_at = $result['data']['expiresAt'];

            Cache::put($cache_key, $token, $exp_at - time() - 300);
            return $token;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            ExtensionHelper::error('Goedge', $e->_toString());
            throw $e;
        } catch (\Exception $e) {
            ExtensionHelper::error('Goedge', $e->_toString());
            throw $e;
        }
        throw new \Exception('Can\'t get token through API, please check if the configuration is correct.');

    }

    public function createServer($user, $params, $order, $product, $configurableOptions)
    {
        $node_cluster_id = (int) ExtensionHelper::getConfig('Goedge', 'node_cluster_id');
        $package_id      = (int) $params['package'];
        $period_type     = $params['period'];
        $price_name      = $period_type . 'Price';

        $create_user_data = [
            'username'      => substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz', ceil(8 / strlen($x)))), 1, 8),
            'password'      => substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ(!@.$%', ceil(10 / strlen($x)))), 1, 12),
            'fullname'      => "{$user->first_name} {$user->last_name}",
            'tel'           => (string) $user->phone,
            'remark'        => "email:{$user->email}",
            'source'        => "paymneter",
            'nodeClusterId' => $node_cluster_id,
        ];
        $result = $this->fetchPost('/UserService/createUser', $create_user_data);
        if (!isset($result['data']['userId'])) {
            ExtensionHelper::error('Goedge', "Unable to create user:" . ($result['message'] ?? "no message"));
            return;
        }

        $user_id = $result['data']['userId'];

        $account_info = $this->fetchPost('/UserAccountService/findEnabledUserAccountWithUserId', ['userId' => $user_id]);

        if (!isset($account_info['data']['userAccount']['id'])) {
            ExtensionHelper::error('Goedge', "Unable to get user account:" . ($result['message'] ?? "no message"));
            $this->fetchPost('/UserService/deleteUser', ['userId' => $user_id]);
            return;
        }

        $account_id = $account_info['data']['userAccount']['id'];

        $plan_info = $this->fetchPost('/PlanService/findEnabledPlan', ['planId' => $package_id]);

        if (!isset($plan_info['data']['plan'])) {
            ExtensionHelper::error('Goedge', "Plan does not exist");
            $this->fetchPost('/UserService/deleteUser', ['userId' => $user_id]);

            return;
        }

        // If the package does not set the price of the corresponding cycle, the cycle type is selected as the largest.
        if (!isset($plan_info['data']['plan'][$price_name])) {
            if (isset($plan_info['data']['plan']['yearlyPrice'])) {
                $price_name  = 'yearlyPrice';
                $period_type = "yearly";
            } elseif (isset($plan_info['data']['plan']['seasonallyPrice'])) {
                $price_name  = 'seasonallyPrice';
                $period_type = "seasonally";
            } elseif (isset($plan_info['data']['plan']['monthlyPrice'])) {
                $price_name  = 'monthlyPrice';
                $period_type = "monthly";
            } else {
                ExtensionHelper::error('Goedge', "Plan no price set");
                $this->fetchPost('/UserService/deleteUser', ['userId' => $user_id]);
                return;
            }
        }

        $plan_price = $plan_info['data']['plan'][$price_name];

        $plan_price    = $plan_price * 1200; //Paymneter doesn't update the product hook, which means it needs to set a very long product expiry time to avoid expiry on the way.
        $features_code = json_decode(base64_decode($plan_info['data']['plan']['featuresJSON']));

        // Add the amount corresponding to the package to the account for the purchase of the package
        $this->fetchPost("/UserAccountService/updateUserAccount", [
            'userAccountId' => $account_id,
            'delta'         => $plan_price,
            'eventType'     => 'charge',
            "description"   => "Paymenter Plan Auto Charge",
            "paramsJSON"    => "",
        ]);

        $user_plan_info = $this->fetchPost('/UserPlanService/buyUserPlan', [
            'userId'      => $user_id,
            'planId'      => $package_id,
            'period'      => $period_type,
            'countPeriod' => 1200, //Paymneter doesn't update the product hook, which means it needs to set a very long product expiry time to avoid expiry on the way.
            "name" => "Paymenter Auto Buy",
        ]);

        if (!isset($user_plan_info['data']['userPlanId'])) {
            ExtensionHelper::error('Goedge', "Unable to purchase plan");
            $this->fetchPost('/UserService/deleteUser', ['userId' => $user_id]);
            return;
        }
        $user_plan_id = $user_plan_info['data']['userPlanId'];

        $cdn_config_params = [
            'userId'          => $user_id,
            "type"            => "httpProxy",
            'name'            => $params['config']['domain'],
            'description'     => "Paymenter Auto Config",
            'serverNamesJSON' => base64_encode(json_encode([
                ["name" => $params['config']['domain'], "type" => "full"],
            ])),
            'httpJSON'        => base64_encode(json_encode([
                "isOn"   => true,
                "listen" => [
                    ["protocol" => "http", "host" => "", "portRange" => "80"],
                ],
            ])),
            'httpsJSON'       => base64_encode(json_encode([
                "isOn"         => true,
                "listen"       => [
                    [
                        "protocol"  => "https",
                        "host"      => "",
                        "portRange" => "443",
                    ],
                ],
                "sslPolicyRef" => [
                    "isOn"        => false,
                    "sslPolicyId" => 0,
                ],
            ])),
            "userPlanId"      => $user_plan_id,
            "nodeClusterId"   => $node_cluster_id,
        ];

        $server_info = $this->fetchPost('/ServerService/createServer', $cdn_config_params);
        if (!isset($server_info['data']['serverId'])) {
            ExtensionHelper::error('Goedge', "Unable to add a domain name to the plan");
            $this->fetchPost('/UserPlanService/deleteUserPlan', ['userPlanId' => $user_plan_id]);
            $this->fetchPost('/UserService/deleteUser', ['userId' => $user_id]);
            return;
        }
        $server_id = $server_info['data']['serverId'];

        $set_user_features_param = [
            'userId'       => $user_id,
            'featureCodes' => $features_code,
        ];
        $this->fetchPost('/UserService/updateUserFeatures', $set_user_features_param);

        ExtensionHelper::setOrderProductConfig('period_type', $period_type, $product->id);
        ExtensionHelper::setOrderProductConfig('package_id', $package_id, $product->id);
        ExtensionHelper::setOrderProductConfig('user_plan_id', $user_plan_id, $product->id);
        ExtensionHelper::setOrderProductConfig('node_cluster_id', $node_cluster_id, $product->id);
        ExtensionHelper::setOrderProductConfig('server_id', $server_id, $product->id);
        ExtensionHelper::setOrderProductConfig('user_id', $user_id, $product->id);
        ExtensionHelper::setOrderProductConfig('username', $create_user_data['username'], $product->id);
        ExtensionHelper::setOrderProductConfig('password', $create_user_data['password'], $product->id);

    }

    public function suspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $user_plan_id   = (int) $params['config']['user_plan_id'];
        $user_plan_info = $this->fetchPost("/UserPlanService/findEnabledUserPlan", [
            'userPlanId' => $user_plan_id,
        ]);

        $update_plan_param = [
            "userPlanId" => $user_plan_id,
            "planId"     => $user_plan_info['data']['userPlan']['planId'],
            // "dayTo"      => $user_plan_info['data']['userPlan']['dayTo'],
            "dayTo"      => date("Y-m-d", time() - 86400),
            "isOn"       => false,
            "name"       => "Paymenter suspend",
        ];
        $this->fetchPost("/UserPlanService/updateUserPlan", $update_plan_param);
    }

    public function unsuspendServer($user, $params, $order, $product, $configurableOptions)
    {
        $user_plan_id   = (int) $params['config']['user_plan_id'];
        $user_plan_info = $this->fetchPost("/UserPlanService/findEnabledUserPlan", [
            'userPlanId' => $user_plan_id,
        ]);

        $update_plan_param = [
            "userPlanId" => $user_plan_id,
            "planId"     => $user_plan_info['data']['userPlan']['planId'],
            "dayTo"      => date("Y-m-d", strtotime("+1200 year")),
            "isOn"       => true,
            "name"       => "Paymenter unsuspend",
        ];

        $this->fetchPost("/UserPlanService/updateUserPlan", $update_plan_param);
    }

    public function terminateServer($user, $params, $order, $product, $configurableOptions)
    {
        $user_plan_id = (int) $params['config']['user_plan_id'];
        $server_id    = (int) $params['config']['server_id'];
        $user_id      = (int) $params['config']['user_id'];
        $this->fetchPost('/ServerService/deleteServer', ['serverId' => $server_id]);
        $this->fetchPost('/UserPlanService/deleteUserPlan', ['userPlanId' => $user_plan_id]);
        $this->fetchPost('/UserService/deleteUser', ['userId' => $user_id]);

    }

    public function getCustomPages($user, $params, $order, $product2, $configurableOptions)
    {
        $user_panel = ExtensionHelper::getConfig('Goedge', 'user_panel');
        return [
            'name'     => 'Goedge',
            'template' => 'goedge::control',
            'data'     => [
                'stats' => '',
                'limit' => ['package' => ""],
                'user'  => [
                    'panel'    => $user_panel,
                    'username' => $params['config']['username'],
                    'password' => $params['config']['password'],
                    'domain'   => $params['config']['domain'],
                ],
            ],
            'pages'    => [
            ],
        ];
    }

}
