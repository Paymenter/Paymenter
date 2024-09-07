<?php

namespace App\Helpers;

use App\Classes\FilamentInput;
use App\Models\Gateway;
use App\Models\Invoice;
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
        $availableExtensions = array_diff(scandir(app_path('Extensions/' . ucfirst($type . 's'))), ['..', '.']);

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
        $extension = '\\App\\Extensions\\' . ucfirst($type) . 's\\' . $extension . '\\' . $extension;

        if (!class_exists($extension)) {
            throw new Exception('Extension not found');
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
        return self::getExtension($type, $extension)->getConfig();
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
        return self::getExtension('server', $server->extension, self::settingsToArray($server->settings))->getProductConfig($values);
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
     * Get every gateway which allows to checkout with
     *
     * @return array
     */
    public static function getCheckoutGateways($items)
    {
        $gateways = [];

        foreach (Gateway::all() as $gateway) {
            if (self::hasFunction($gateway, 'canUseGateway')) {
                if (self::getExtension('gateway', $gateway, $gateway->settings)->canUseGateway($items)) {
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
            $properties[$config->configOption->env_variable] = $config->configValue->value;
        }

        return $properties;
    }

    /**
     * Create server
     */
    public static function createServer(Service $service)
    {
        $server = $service->product->server;

        return self::getExtension('server', $server->extension, $server->settings)->createServer($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service));
    }

    /**
     * Suspend server
     */
    public static function suspendServer(Service $service)
    {
        $server = $service->product->server;

        return self::getExtension('server', $server->extension, $server->settings)->suspendServer($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service));
    }

    /**
     * Unsuspend server
     */
    public static function unsuspendServer(Service $service)
    {
        $server = $service->product->server;

        return self::getExtension('server', $server->extension, $server->settings)->unsuspendServer($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service));
    }

    /**
     * Terminate server
     */
    public static function terminateServer(Service $service)
    {
        $server = $service->product->server;

        return self::getExtension('server', $server->extension, $server->settings)->terminateServer($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service));
    }
}
