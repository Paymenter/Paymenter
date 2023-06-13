<?php

namespace App\Helpers;

use App\Models\ConfigurableOption;
use App\Models\ConfigurableOptionInput;
use App\Models\User;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Extension;
use App\Models\OrderProduct;
use App\Models\OrderProductConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\View;

class ExtensionHelper
{
    /**
     * Called when a new invoice is accepted.
     *
     * @param int $id ID of the invoice
     *
     * @return void
     */
    public static function paymentDone($id)
    {
        $invoice = Invoice::findOrFail($id);
        foreach ($invoice->items()->get() as $item) {
            $product = $item->product()->get()->first();
            if (!$product) {
                continue;
            }

            if ($product->status == 'suspended') {
                ExtensionHelper::unsuspendServer($product);
            }
            if ($product->status == 'pending') {
                ExtensionHelper::createServer($product);
            }
            if ($product->status == 'pending' || $product->status == 'suspended') {
                if ($product->billing_cycle) {
                    if ($product->billing_cycle == 'monthly') {
                        $product->expiry_date = Carbon::now()->addMonth();
                    } elseif ($product->billing_cycle == 'quarterly') {
                        $product->expiry_date = Carbon::now()->addMonths(3);
                    } elseif ($product->billing_cycle == 'semi_annually') {
                        $product->expiry_date = Carbon::now()->addMonths(6);
                    } elseif ($product->billing_cycle == 'annually') {
                        $product->expiry_date = Carbon::now()->addYear();
                    } elseif ($product->billing_cycle == 'biennially') {
                        $product->expiry_date = Carbon::now()->addYears(2);
                    } elseif ($product->billing_cycle == 'triennially') {
                        $product->expiry_date = Carbon::now()->addYears(3);
                    }
                }
            } else {
                if ($product->billing_cycle) {
                    if ($product->billing_cycle == 'monthly') {
                        $product->expiry_date = Carbon::parse($product->expiry_date)->addMonth();
                    } elseif ($product->billing_cycle == 'quarterly') {
                        $product->expiry_date = Carbon::parse($product->expiry_date)->addMonths(3);
                    } elseif ($product->billing_cycle == 'semi_annually') {
                        $product->expiry_date = Carbon::parse($product->expiry_date)->addMonths(6);
                    } elseif ($product->billing_cycle == 'annually') {
                        $product->expiry_date = Carbon::parse($product->expiry_date)->addYear();
                    } elseif ($product->billing_cycle == 'biennially') {
                        $product->expiry_date = Carbon::parse($product->expiry_date)->addYears(2);
                    } elseif ($product->billing_cycle == 'triennially') {
                        $product->expiry_date = Carbon::parse($product->expiry_date)->addYears(3);
                    }
                }
            }
            $product->status = 'paid';
            $product->save();
        }

        if ($invoice->status == 'paid') {
            return;
        }
        $invoice->status = 'paid';
        $invoice->paid_at = now();
        $invoice->save();
    }

    /**
     * Called when a payment is failed.
     *
     * @param int $id ID of the order
     *
     * @return void
     */
    public function paymentFailed($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'failed';
        $order->save();
    }

    /**
     * Called when a payment is cancelled.
     *
     * @param int $id ID of the order
     *
     * @return void
     */
    public function paymentCancelled($id)
    {
        $order = Order::find($id);
        $order->status = 'cancelled';
        $order->save();
    }

    /**
     * Called when you got a error.
     *
     * @return void
     */
    public static function error($extension, $message)
    {
        // Convert message to string
        if (is_array($message)) {
            $message = json_encode($message);
        }
        Log::error($extension . ': ' . $message);
    }

    /**
     * Called when a new order is accepted
     * ```php
     * ExtensionHelper::getConfig('paymenter', 'paymenter');
     * ```.
     *
     * @param string $name Name of the extension
     * @param string $key Name of the config
     *
     * @return string
     */
    public static function getConfig($name, $key)
    {
        $extension = Extension::where('name', $name)->first();
        if (!$extension) {
            Extension::create([
                'name' => $name,
                'enabled' => false,
                'type' => 'notset',
            ]);
            $extension = Extension::where('name', $name)->first();
        }
        $config = $extension->getConfig()->where('key', $key)->first();
        if (!$config) {
            return;
        }
        try {
            return Crypt::decryptString($config->value);
        } catch (DecryptException $e) {
            return $config->value;
        }
    }

    /**
     * Sets the config of an extension
     * ```php
     * ExtensionHelper::setConfig('paymenter', 'paymenter', 'paypal');
     * ```.
     *
     * @param string $name Name of the extension
     * @param string $key Name of the config
     * @param string $value Value of the config
     *
     * @return void
     */
    public static function setConfig($name, $key, $value)
    {
        $extension = Extension::where('name', $name)->first();
        if (!$extension) {
            Extension::create([
                'name' => $name,
                'enabled' => false,
                'type' => 'notset',
            ]);
            $extension = Extension::where('name', $name)->first();
        }
        $config = $extension->getConfig()->where('key', $key)->first();

        $value = Crypt::encryptString($value);

        if (!$config) {
            $extension->getConfig()->create([
                'key' => $key,
                'value' => $value,
            ]);
        } else {
            $config->value = $value;
            $config->save();
        }
    }

    public static function getCurrency()
    {
        return config('settings::currency') ?? 'USD';
    }

    public static function getProductConfig($name, $key, $id)
    {
        $extension = Extension::where('name', $name)->first();
        if (!$extension) {
            Extension::create([
                'name' => $name,
                'enabled' => false,
                'type' => 'server',
            ]);
            $extension = Extension::where('name', $name)->first();
        }

        $config = $extension->getServer()->where('product_id', $id)->where('extension', $extension->id)->where('name', $key)->first();
        if (!$config) {
            $extension->getServer()->create([
                'name' => $key,
                'value' => '',
                'product_id' => $id,
                'extension' => $extension->id,
            ]);
            $config = $extension->getServer()->where('product_id', $id)->where('extension', $extension->id)->where('name', $key)->first();
        }

        return $config->value;
    }

    /**
     * Creates or updates a Product Config.
     *
     * @return void
     */
    public static function setOrderProductConfig($key, $value, $id)
    {
        $config = OrderProductConfig::where('order_product_id', $id)->where('key', $key)->first();
        if (!$config) {
            OrderProductConfig::create([
                'order_product_id' => $id,
                'key' => $key,
                'value' => $value,
            ]);
        } else {
            $config->value = $value;
            $config->save();
        }
    }

    public static function getPaymentMethod($id, $total, $products, $orderId)
    {
        $extension = Extension::where('id', $id)->first();
        if (!$extension) {
            return false;
        }
        if (!file_exists(app_path() . '/Extensions/Gateways/' . $extension->name . '/index.php')) {
            return false;
        }
        include_once app_path() . '/Extensions/Gateways/' . $extension->name . '/index.php';
        $function = $extension->name . '_pay';
        $pay = $function($total, $products, $orderId);

        return $pay;
    }

    public static function loadConfiguration(Product $product, OrderProduct $product2)
    {
        $settings = $product->settings ?? collect();
        $config = [];
        foreach ($settings as $setting) {
            $config[$setting->name] = $setting->value;
        }
        $config['config_id'] = $product->id;
        $config['config'] = [];
        foreach ($product2->config as $config2) {
            if ($config2->is_configurable_option) {
                continue;
            }
            $config['config'][$config2->key] = $config2->value;
        }
        return $config;
    }

    public static function loadConfigurableOptions(OrderProduct $product2)
    {
        $configurableOptions = [];
        foreach ($product2->config as $config2) {
            if (!$config2->is_configurable_option) {
                continue;
            }
            $option = ConfigurableOption::where('id', $config2->key)->first();
            if (!$option) {
                continue;
            }
            $option->original_name = $option->name;
            $option->name = explode('|', $option->name)[0] ?? $option->name;
            $value = ConfigurableOptionInput::where('id', $config2->value)->first();
            $configurableOptions[$option->name] = $value ? $value->name : $config2->value;
        }
        return $configurableOptions;
    }

    public static function createServer(OrderProduct $product2)
    {
        $order = $product2->order()->first();
        $product = Product::findOrFail($product2->product_id);
        if (!isset($product->server_id)) {
            return;
        }
        $extension = Extension::where('id', $product->server_id)->first();
        if (!$extension) {
            return false;
        }
        include_once app_path() . '/Extensions/Servers/' . $extension->name . '/index.php';
        $extensionName = $extension->name;
        $extensionFunction = $extensionName . '_createServer';
        if (!function_exists($extensionFunction)) {
            self::error($extensionName, 'Function ' . $extensionFunction . ' does not exist! (createServer)');
            return;
        }
        $config = self::loadConfiguration($product, $product2);
        $configurableOptions = self::loadConfigurableOptions($product2);
        $user = User::findOrFail($order->client);
        try {
            $extensionFunction($user, $config, $order, $product2, $configurableOptions);
        } catch (\Exception $e) {
            self::error($extensionName, 'Error creating server: ' . $e->getMessage());
        }
    }

    public static function suspendServer(OrderProduct $product2)
    {
        $order = $product2->order()->first();
        $product = Product::findOrFail($product2->product_id);
        if (!isset($product->server_id)) {
            return;
        }
        $extension = Extension::where('id', $product->server_id)->first();
        if (!$extension) {
            return false;
        }
        if (!file_exists(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php')) {
            return false;
        }
        include_once app_path() . '/Extensions/Servers/' . $extension->name . '/index.php';
        $config = self::loadConfiguration($product, $product2);
        $configurableOptions = self::loadConfigurableOptions($product2);
        $user = User::findOrFail($order->client);
        $function = $extension->name . '_suspendServer';
        if (!function_exists($function)) {
            self::error($extension->name, 'Function ' . $function . ' does not exist! (suspendServer)');
            return;
        }
        try {
            $function($user, $config, $order, $product2, $configurableOptions);
        } catch (\Exception $e) {
            self::error($extension->name, 'Error suspending server: ' . $e->getMessage());
        }
    }

    public static function unsuspendServer(OrderProduct $product2)
    {
        $order = $product2->order()->first();

        $product = Product::findOrFail($product2->product_id);
        if (!isset($product->server_id)) {
            return;
        }
        $extension = Extension::where('id', $product->server_id)->first();
        if (!$extension) {
            return false;
        }
        if (!file_exists(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php')) {
            return false;
        }
        include_once app_path() . '/Extensions/Servers/' . $extension->name . '/index.php';
        $config = self::loadConfiguration($product, $product2);
        $configurableOptions = self::loadConfigurableOptions($product2);
        $user = User::findOrFail($order->client);
        $function = $extension->name . '_unsuspendServer';
        try {
            $function($user, $config, $order, $product2, $configurableOptions);
        } catch (\Exception $e) {
            ExtensionHelper::error($extension->name, 'Error creating server: ' . $e->getMessage());
        }
    }

    public static function terminateServer(OrderProduct $product2)
    {
        $order = $product2->order()->first();

        $product = Product::findOrFail($product2->product_id);
        if (!isset($product->server_id)) {
            return;
        }
        $extension = Extension::where('id', $product->server_id)->first();
        if (!$extension) {
            return false;
        }
        if (!file_exists(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php')) {
            return false;
        }
        include_once app_path() . '/Extensions/Servers/' . $extension->name . '/index.php';
        $config = self::loadConfiguration($product, $product2);
        $configurableOptions = self::loadConfigurableOptions($product2);
        $user = User::findOrFail($order->client);
        $function = $extension->name . '_terminateServer';
        try {
            $function($user, $config, $order, $product2, $configurableOptions);
        } catch (\Exception $e) {
            ExtensionHelper::error($extension->name, 'Error creating server: ' . $e->getMessage());
        }
    }

    public static function getLink(OrderProduct $product)
    {
        if (!isset($product->product()->get()->first()->server_id)) {
            return false;
        }
        $extension = Extension::where('id', $product->product()->get()->first()->server_id)->first();
        if (!$extension) {
            return false;
        }
        // Check if file exists
        if (!file_exists(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php')) {
            return false;
        }
        include_once app_path() . '/Extensions/Servers/' . $extension->name . '/index.php';
        $settings = $product->product->settings()->get();
        $config = [];
        foreach ($settings as $setting) {
            $config[$setting->name] = $setting->value;
        }
        $config['config_id'] = $product->product->id;
        foreach ($product->config()->get() as $config2) {
            $config['config'][$config2->key] = $config2->value;
        }
        $user = User::findOrFail($product->order->client);
        $function = $extension->name . '_getLink';
        if (!function_exists($function)) {
            return false;
        }
        $link = $function($user, $config, $product->order()->get()->first(), $product);
        
        return $link;
    }

    /**
     * Get the product configuration for the admin area
     * 
     * @param Product $product
     * 
     * @return array
     */
    public static function getProductConfiguration(Product $product)
    {
        if(!isset($product->server_id)){
            return [];
        }
        $extension = Extension::where('id', $product->server_id)->first();
        if(!$extension){
            return [];
        }
        if(!file_exists(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php')){
            return [];
        }
        include_once app_path() . '/Extensions/Servers/' . $extension->name . '/index.php';
        $settings = $product->settings;
        $config = [];
        foreach($settings as $setting){
            $config[$setting->name] = $setting->value;
        }
        $config['config_id'] = $product->id;
        
        $function = $extension->name . '_getProductConfig';
        if(!function_exists($function)){
            return [];
        }
        $config =  $function($config);
        return json_decode(json_encode($config));
    }

    /**
     * Get custom pages for client area
     * 
     * @param OrderProduct $product
     * 
     * @return array
     */
    public static function getCustomPages(OrderProduct $product2)
    {
        $order = $product2->order()->first();

        $product = Product::findOrFail($product2->product_id);
        if (!isset($product->server_id)) {
            return [];
        }
        $extension = Extension::where('id', $product->server_id)->first();
        if (!$extension) {
            return [];
        }
        if (!file_exists(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php')) {
            return [];
        }
        include_once app_path() . '/Extensions/Servers/' . $extension->name . '/index.php';
        $config = self::loadConfiguration($product, $product2);
        $configurableOptions = self::loadConfigurableOptions($product2);
        $user = User::findOrFail($order->client);
        $function = $extension->name . '_getCustomPages';
        View::addNamespace(strtolower($extension->name), app_path() . '/Extensions/Servers/' . $extension->name . '/views');
        try {
            return $function($user, $config, $order, $product2, $configurableOptions);
        } catch (\Exception $e) {
            ExtensionHelper::error($extension->name, 'Error getting pages ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in file ' . $e->getFile());
            return [];
        } 
    }
    

    /**
     * Get all parameters for a order product
     * 
     * @param OrderProduct $product
     * 
     * @return object
     */
    public static function getParameters(OrderProduct $product2)
    {
        $product = Product::findOrFail($product2->product_id);
        $config = self::loadConfiguration($product, $product2);
        $user = User::findOrFail($product2->order->client);
        $configurableOptions = self::loadConfigurableOptions($product2);
        
        return (object) [
            'user' => $user,
            'config' => $config,
            'order' => $product2->order,
            'product' => $product2,
            'configurableOptions' => $configurableOptions,
        ];
    }
}
