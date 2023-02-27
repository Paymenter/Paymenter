<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Order;
use App\Models\Invoices;
use App\Models\Product;
use App\Models\Extension;
use App\Models\OrderProducts;
use App\Models\OrderProductsConfig;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ExtensionHelper
{
    /**
     * Called when a new order is accepted.
     *
     * @param int $id ID of the order
     *
     * @return void
     */
    public static function paymentDone($id)
    {
        $invoice = Invoices::findOrFail($id);
        if ($invoice->status == 'paid') {
            return;
        }
        $invoice->status = 'paid';
        $invoice->paid_at = now();
        $invoice->save();
        $order = Order::findOrFail($invoice->order_id);
        if ($order->status == 'paid') {
            return;
        }
        if ($order->status == 'suspended') {
            ExtensionHelper::unsuspendServer($order);
        }
        if ($order->status == 'pending') {
            ExtensionHelper::createServer($order);
        }
        $order->status = 'paid';
        $order->save();
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
        $config = OrderProductsConfig::where('order_product_id', $id)->where('key', $key)->first();
        if (!$config) {
            OrderProductsConfig::create([
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

    public static function createServer(Order $order)
    {
        foreach ($order->products()->get() as $product2) {
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
            $settings = $product->settings()->get();
            $config = [];
            foreach ($settings as $setting) {
                $config[$setting->name] = $setting->value;
            }
            $config['config_id'] = $product->id;
            foreach ($product2->config()->get() as $config2) {
                $config['config'][$config2->key] = $config2->value;
            }
            $user = User::findOrFail($order->client);
            $function = $extension->name . '_createServer';
            $function($user, $config, $order, $product2);

            return true;
        }
    }

    public static function suspendServer(Order $order)
    {
        foreach ($order->products()->get() as $product2) {
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
            $settings = $product->settings()->get();
            $config = [];
            foreach ($settings as $setting) {
                $config[$setting->name] = $setting->value;
            }
            $config['config_id'] = $product->id;
            foreach ($product2->config()->get() as $config2) {
                $config['config'][$config2->key] = $config2->value;
            }
            $user = User::findOrFail($order->client);
            $function = $extension->name . '_suspendServer';
            $function($user, $config, $order, $product2);

            return true;
        }
    }

    public static function unsuspendServer(Order $order)
    {
        foreach ($order->products()->get() as $product2) {
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
            $settings = $product->settings()->get();
            $config = [];
            foreach ($settings as $setting) {
                $config[$setting->name] = $setting->value;
            }
            $config['config_id'] = $product->id;
            foreach ($product2->config()->get() as $config2) {
                $config['config'][$config2->key] = $config2->value;
            }
            $user = User::findOrFail($order->client);
            $function = $extension->name . '_unsuspendServer';
            $function($user, $config, $order, $product2);

            return true;
        }
    }

    public static function terminateServer(Order $order)
    {
        foreach ($order->products()->get() as $product2) {
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
            $settings = $product->settings()->get();
            $config = [];
            foreach ($settings as $setting) {
                $config[$setting->name] = $setting->value;
            }
            $config['config_id'] = $product->id;
            foreach ($product2->config()->get() as $config2) {
                $config['config'][$config2->key] = $config2->value;
            }
            $user = User::findOrFail($order->client);
            $function = $extension->name . '_terminateServer';
            $function($user, $config, $order, $product2);

            return true;
        }
    }

    public static function getLink(OrderProducts $product)
    {
        if(!isset($product->product()->get()->first()->server_id)) {
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
        $link = $function($user, $config, $product->order()->get()->first());

        return $link;
    }
}
