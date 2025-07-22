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
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Cache;

class ExtensionHelper
{
    /**
     * Used to read all Extensions in app/Extensions with or without type (e.g. 'gateway' or 'server' or 'other' (for non-gateway/server extensions))
     *
     * @param  string|null  $type
     * @return array
     */
    public static function getExtensions($type = null)
    {
        // Check how long this takes
        $extensions = self::getAvailableExtensions();

        if ($type && $type == 'other') {
            // Filter out gateways and servers
            $extensions = array_filter($extensions, fn ($extension) => !in_array($extension['type'], ['gateway', 'server']));

            return $extensions;
        } elseif ($type) {
            $type = strtolower($type);

            return array_filter($extensions, fn ($extension) => $extension['type'] === $type);
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
    public static function getConfig($type, $extension, $config = [])
    {
        if (empty($config)) {
            $typeClass = ($type == 'gateway') ? Gateway::class : (($type == 'server') ? Server::class : Extension::class);
            $config = $typeClass::where('extension', $extension)->exists()
                ? $typeClass::where('extension', $extension)->first()->settings->pluck('value', 'key')->toArray()
                : [];
        }

        return self::getExtension($type, $extension)->getConfig($config);
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
    public static function getCheckoutConfig(Product $product, $values = [])
    {
        $server = $product->server;
        if (!$server) {
            return [];
        }

        return self::call($server, 'getCheckoutConfig', [$product, $values, self::settingsToArray($product->settings)], mayFail: true) ?? [];
    }

    /**
     * Get all extensions which are not gateways or servers with their settings
     *
     * @return array
     */
    public static function getAvailableExtensions()
    {
        $extensions = [];

        $classmap = require_once base_path('vendor/composer/autoload_classmap.php');

        // Magic code so we can also support extensions that don't reside in the extensions folder
        foreach ($classmap as $class => $path) {
            if (strpos($class, 'Paymenter\\Extensions\\') !== 0) {
                continue;
            }

            // Example: Paymenter\Extensions\Whatevers\SomeExtension\SomeExtension
            $parts = explode('\\', $class);

            // Must have at least: Paymenter, Extensions, <Type>s, <Name>, <Class>
            if (count($parts) < 5) {
                continue;
            }

            $typePlural = $parts[2];

            $type = strtolower(rtrim($typePlural, 's'));
            $name = $parts[3];

            // Only add the main extension class (class name matches extension folder)
            if ($parts[4] !== $name) {
                continue;
            }

            $extensions[] = [
                'name' => $name,
                'type' => $type,
            ];
        }

        // Newly created extensions sometimes don't have a classmap entry, so we also check the filesystem
        $extensionPath = base_path('extensions');
        $typeFolders = glob($extensionPath . '/*', GLOB_ONLYDIR);
        foreach ($typeFolders as $typeFolder) {
            $type = strtolower(rtrim(basename($typeFolder), 's'));
            $extensionDirs = glob($typeFolder . '/*', GLOB_ONLYDIR);

            foreach ($extensionDirs as $extensionDir) {
                $name = basename($extensionDir);

                // CHeck if already added
                if (in_array($name, array_column($extensions, 'name')) && in_array($type, array_column($extensions, 'type'))) {
                    continue;
                }

                // Check if the class exists
                if (class_exists('\\Paymenter\\Extensions\\' . ucfirst($type) . 's\\' . $name . '\\' . $name)) {
                    $extensions[] = [
                        'name' => $name,
                        'type' => $type,
                    ];
                }
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
     * @param  Extension  $extension
     * @return object
     */
    public static function getConfigAsInputs(string $type, ?string $name, $config = [])
    {
        if (!$name) {
            return [];
        }

        $settings = [];

        try {
            foreach (self::getConfig($type, $name, $config) as $key => $config) {
                $config['name'] = 'settings.' . $config['name'];
                $settings[] = FilamentInput::convert($config);
            }
        } catch (\Exception $e) {
            $settings[] = Placeholder::make('error')->content($e->getMessage());
            // Handle exception
        }

        return $settings;
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

    protected static function prepareForSerialization($values)
    {
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $values[$key] = self::prepareForSerialization($value);
            }

            return $values;
        }

        if ($values instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            // Store the file and use the path, or just use the filename if already stored
            return $values->getRealPath() ?: (string) $values;
        }

        return $values;
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

        $key = $server->extension . $server->id . md5(serialize(self::prepareForSerialization($values)));

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
                if (self::getExtension('gateway', $gateway->extension, $gateway->settings)->canUseGateway($items, $type)) {
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
        $function = isset($view['function']) ? $view['function'] : 'getView';

        $server = self::checkServer($service, $function);

        return self::getExtension('server', $server->extension, $server->settings)->$function($service, self::settingsToArray($service->product->settings), self::getServiceProperties($service), $view['name']);
    }
}
