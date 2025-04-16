<?php

namespace App\Helpers;

use App\Classes\FilamentInput;
use App\Models\Extension;
use App\Models\Gateway;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Server;
use App\Models\Service;
use Exception;
use Illuminate\Support\Facades\Cache;

class ExtensionHelper
{
    /**
     * Used to read all Extensions in app/Extensions with or without type (e.g. 'gateway' or 'server')
     *
     * @param  string|null  $type
     * @return array
     */
    private static function getExtensions($type)
    {
        // Read app/Extensions directory
        $availableExtensions = array_diff(scandir(base_path('extensions/' . ucfirst($type . 's'))), ['..', '.']);

        // Read settings
        foreach ($availableExtensions as $key => $extension) {
            $extensions[] = [
                'name' => $extension,
                'settings' => self::getConfig($type, $extension),
            ];
        }

        return $extensions;
    }

    /**
     * Get extension and return new instance
     *
     * @param  string  $type
     * @param  string  $extension
     * @return object
     */
    public static function getExtension($type, $extension, $config = [])
    {
        $extension = '\\Paymenter\\Extensions\\' . ucfirst($type) . 's\\' . $extension . '\\' . $extension;

        if (!class_exists($extension)) {
            throw new Exception('Extension "' . $extension . '" not found');
        }

        if (!is_array($config)) {
            $config = self::settingsToArray($config);
        }

        return new $extension($config);
    }

    /**
     * Get available settings
     *
     * @return array
     */
    public static function getConfig($type, $extension)
    {
        $typeClass = ($type == 'gateway') ? Gateway::class : Server::class;
        $currentConfig = $typeClass::where('extension', $extension)->exists()
            ? $typeClass::where('extension', $extension)->first()->settings->pluck('value', 'key')->toArray()
            : [];

        return self::getExtension($type, $extension)->getConfig($currentConfig);
    }

    /**
     * Has function
     *
     * @param  object  $extension
     * @param  string  $function
     */
    public static function hasFunction($extension, $function)
    {
        return method_exists(self::getExtension($extension->type, $extension->extension, $extension->settings), $function);
    }

    /**
     * Test connection
     *
     * @return string
     */
    public static function testConfig($extension, $values)
    {
        return self::getExtension($extension->type, $extension->extension, $values)->testConfig();
    }

    /**
     * Get checkout configuration
     *
     * @return array
     */
    public static function getCheckoutConfig(Product $product)
    {
        $server = $product->server;
        if (!$server) {
            return [];
        }

        return self::call($server, 'getCheckoutConfig', [$product], mayFail: true) ?? [];
    }

    /**
     * Get available gateways
     *
     * @return array
     */
    public static function getAvailableGateways()
    {
        return self::getExtensions('gateway');
    }

    /**
     * Get available servers
     *
     * @return array
     */
    public static function getAvailableServers()
    {
        return self::getExtensions('server');
    }

    /**
     * Get all extensions which are not gateways or servers with their settings
     *
     * @return array
     */
    public static function getAvailableExtensions()
    {
        $extensions = [];

        foreach (scandir(base_path('extensions')) as $extension) {
            if (in_array($extension, ['.', '..', 'Gateways', 'Servers'])) {
                continue;
            }

            $type = strtolower($extension);
            // Remove the 's' from  end of the type
            $type = substr($type, 0, -1);

            foreach (scandir(base_path('extensions/' . $extension)) as $extension) {
                if (in_array($extension, ['.', '..'])) {
                    continue;
                }

                $extensions[] = [
                    'name' => $extension,
                    'type' => $type,
                    'settings' => self::getConfig($type, $extension),
                ];
            }
        }

        return $extensions;
    }

    public static function call($extension, $function, $args = [], $mayFail = false)
    {
        try {
            if (!self::hasFunction($extension, $function)) {
                throw new Exception('Function not found');
            }

            return self::getExtension($extension->type, $extension->extension, $extension->settings)->$function(...$args);
        } catch (Exception $e) {
            if (!$mayFail) {
                throw $e;
            }
        }
    }

    public static function callService(Service $service, $function, $args = [], $mayFail = false)
    {
        $server = $service->product->server;

        if (!$server) {
            if ($mayFail) {
                throw new Exception('No server assigned to this product');
            } else {
                return;
            }
        }

        return self::call($server, $function, [$service, self::settingsToArray($service->product->settings), self::getServiceProperties($service), ...$args], $mayFail);
    }

    /**
     * Convert extensions to options
     *
     * @param  array  $extensions
     * @return object
     */
    public static function convertToOptions($extensions)
    {
        $options = [];
        $settings = ['default' => []];
        foreach ($extensions as $extension) {
            $options[$extension['name']] = $extension['name'];
            foreach ($extension['settings'] as $setting) {
                $setting['name'] = 'settings.' . $setting['name'];
                $settings[$extension['name']][] = FilamentInput::convert($setting, true);
            }
        }

        return (object) ['options' => $options, 'settings' => $settings];
    }

    /**
     * Get available settings
     *
     * @return array
     */
    public static function getProductConfig($server, $values = [])
    {
        return self::call($server, 'getProductConfig', [$values]);
    }

    /**
     * Get available settings
     *
     * @return array
     */
    public static function getProductConfigOnce($server, $values = [])
    {
        static $config = [];

        $config = Cache::get('product_config', []);

        $key = $server->extension . $server->id . md5(serialize($values));

        if (!isset($config[$key])) {
            $config[$key] = self::getProductConfig($server, $values);
        }

        Cache::put('product_config', $config, 60);

        return $config[$key];
    }

    /**
     * Convert settings to array
     *
     * @param  mixed  $settings
     * @return array
     */
    public static function settingsToArray($settings)
    {
        $settingsArray = [];

        if ($settings instanceof \Illuminate\Database\Eloquent\Collection) {
            // If $settings is a collection of models
            foreach ($settings as $setting) {
                $settingsArray[$setting->key] = $setting->value;
            }
        } elseif ($settings instanceof \Illuminate\Database\Eloquent\Model) {
            // If $settings is a single model
            $settingsArray[$settings->name] = $settings->value;
        }

        return $settingsArray ?? $settings;
    }

    /**
     * Register a new middleware.
     *
     * @param  string  $middleware
     * @param  string  $group
     * @return Router
     */
    public static function registerMiddleware($middleware, $group = 'web')
    {
        return app('router')->pushMiddlewareToGroup($group, $middleware);
    }

    /**
     * Get every gateway which allows to checkout with
     *
     * @return array
     */
    public static function getCheckoutGateways($items, $type)
    {
        $gateways = [];

        foreach (Gateway::all() as $gateway) {
            if (self::hasFunction($gateway, 'canUseGateway')) {
                if (self::getExtension('gateway', $gateway, $gateway->settings)->canUseGateway($items, $type)) {
                    $gateways[] = $gateway;
                }
            } else {
                $gateways[] = $gateway;
            }
        }

        return $gateways;
    }

    /**
     * Get payment url or view
     */
    public static function pay($gateway, $invoice)
    {
        return self::getExtension('gateway', $gateway->extension, $gateway->settings)->pay($invoice, $invoice->remaining);
    }

    /**
     * Add payment to invoice
     */
    public static function addPayment($invoice, $gateway, $amount, $fee = null, $transactionId = null)
    {
        if (isset($gateway)) {
            $gateway = Gateway::where('extension', $gateway)->first();
        }

        $invoice = Invoice::findOrFail($invoice);
        if (!$transactionId) {
            $invoice->transactions()->create([
                'gateway_id' => $gateway ? $gateway->id : null,
                'amount' => $amount,
                'fee' => $fee,
            ]);
        } else {
            $invoice->transactions()->updateOrCreate(
                [
                    'transaction_id' => $transactionId,
                ],
                [
                    'gateway_id' => $gateway ? $gateway->id : null,
                    'amount' => $amount,
                    'fee' => $fee,
                ]
            );
        }

        if ($invoice->remaining <= 0 && $invoice->status !== 'paid') {
            $invoice->status = 'paid';
            $invoice->save();
        }
    }

    /**
     * Cancel subscription
     */
    public static function cancelSubscription(Service $service)
    {
        foreach (Gateway::all() as $gateway) {
            if (self::hasFunction($gateway, 'cancelSubscription')) {
                if (self::getExtension('gateway', $gateway->extension, $gateway->settings)->cancelSubscription($service)) {
                    return true;
                }
            }
        }

        return false;
    }

    /* SERVER RELATED FUNCTIONS */

    /**
     * Get both properties and config options from order product and smash them together
     */
    public static function getServiceProperties(Service $service)
    {
        $properties = [];
        foreach ($service->properties as $property) {
            $properties[$property->key] = $property->value;
        }
        foreach ($service->configs as $config) {
            $properties[$config->configOption->env_variable] = $config->configValue->env_variable ?? $config->configValue->name;
        }

        return $properties;
    }

    protected static function checkServer(Service $service, $action)
    {
        $server = $service->product->server;

        if (!$server) {
            throw new Exception('No server assigned to this product');
        }

        // Does server support this action?
        if (!self::hasFunction($server, $action)) {
            throw new Exception('Server does not support the action: ' . $action);
        }

        return $server;
    }

    /**
     * Create server
     */
    public static function createServer(Service $service)
    {
        $server = self::checkServer($service, 'createServer');

        return self::getExtension('server', $server->extension, $server->settings)->createServer($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service));
    }

    /**
     * Suspend server
     */
    public static function suspendServer(Service $service)
    {
        $server = self::checkServer($service, 'suspendServer');

        return self::getExtension('server', $server->extension, $server->settings)->suspendServer($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service));
    }

    /**
     * Unsuspend server
     */
    public static function unsuspendServer(Service $service)
    {
        $server = self::checkServer($service, 'unsuspendServer');

        return self::getExtension('server', $server->extension, $server->settings)->unsuspendServer($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service));
    }

    /**
     * Terminate server
     */
    public static function terminateServer(Service $service)
    {
        $server = self::checkServer($service, 'terminateServer');

        return self::getExtension('server', $server->extension, $server->settings)->terminateServer($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service));
    }

    /**
     * Upgrade server
     */
    public static function upgradeServer(Service $service)
    {
        $server = self::checkServer($service, 'upgradeServer');

        return self::getExtension('server', $server->extension, $server->settings)->upgradeServer($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service));
    }

    /**
     * Get actions for service
     */
    public static function getActions(Service $service)
    {
        $server = self::checkServer($service, 'getActions');

        return self::getExtension('server', $server->extension, $server->settings)->getActions($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service));
    }

    /**
     * Get actions for service
     */
    public static function getView(Service $service, $view)
    {
        $server = self::checkServer($service, 'getView');

        return self::getExtension('server', $server->extension, $server->settings)->getView($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service), $view);
    }
}
