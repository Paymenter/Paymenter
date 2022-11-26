<?php

namespace App\Helpers;

use App\Models\Orders;
use App\Models\Products;
use App\Models\User;
use App\Models\Invoices;
use App\Models\Extensions;
use App\Models\OrderProductsConfig;
use Illuminate\Http\Request;

class ExtensionHelper
{
    /**
     * Called when a new order is accepted
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
        $order = Orders::findOrFail($invoice->order_id);
        $order->status = 'paid';
        $order->save();
        ExtensionHelper::createServer($order);
    }

    function paymentFailed($id)
    {
        $order = Orders::findOrFail($id);
        $order->status = 'failed';
        $order->save();
    }

    function paymentCancelled($id)
    {
        $order = Orders::find($id);
        $order->status = 'cancelled';
        $order->save();
    }

    /**
     * Called when you got a error
     * @return void
     */
    public static function error($extension, $message)
    {
        return;
    }

    /**
     * Called when a new order is accepted
     * ```php
     * ExtensionHelper::getConfig('paymenter', 'paymenter');
     * ```
     * @return String
     */
    public static function getConfig($name, $key)
    {
        $extension = Extensions::where('name', $name)->first();
        if (!$extension) {
            Extensions::create([
                'name' => $name,
                'enabled' => false,
                'type' => 'notset'
            ]);
            $extension = Extensions::where('name', $name)->first();
        }
        $config = $extension->getConfig()->where('key', $key)->first();
        if (!$config) {
            return;
        }

        return $config->value;
    }

    public static function setConfig($name, $key, $value)
    {
        $extension = Extensions::where('name', $name)->first();
        if (!$extension) {
            Extensions::create([
                'name' => $name,
                'enabled' => false,
                'type' => 'notset'
            ]);
            $extension = Extensions::where('name', $name)->first();
        }
        $config = $extension->getConfig()->where('key', $key)->first();
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

    public static function getProductConfig($name, $key, $id)
    {
        $extension = Extensions::where('name', $name)->first();
        if (!$extension) {
            Extensions::create([
                'name' => $name,
                'enabled' => false,
                'type' => 'server'
            ]);
            $extension = Extensions::where('name', $name)->first();
        }

        $config = $extension->getServer()->where('product_id', $id)->where('extension', $extension->id)->where('name', $key)->first();
        if (!$config) {
            $extension->getServer()->create([
                'name' => $key,
                'value' => '',
                'product_id' => $id,
                'extension' => $extension->id
            ]);
            $config = $extension->getServer()->where('product_id', $id)->where('extension', $extension->id)->where('name', $key)->first();
        }
        return $config->value;
    }

    /**
     * Creates or updates a Product Config
     * @return void
     */
    public static function setOrderProductConfig($key, $value, $id)
    {
        $config = OrderProductsConfig::where('order_product_id', $id)->where('key', $key)->first();
        if (!$config) {
            OrderProductsConfig::create([
                'order_product_id' => $id,
                'key' => $key,
                'value' => $value
            ]);
        } else {
            $config->value = $value;
            $config->save();
        }
    }

    public static function getPaymentMethod($id, $total, $products, $orderId)
    {
        $extension = Extensions::where('id', $id)->first();
        if (!$extension) {
            return false;
        }
        include_once(app_path() . '/Extensions/Gateways/' . $extension->name . '/index.php');
        $function = $extension->name . '_pay';
        $pay = $function($total, $products, $orderId);

        return $pay;
    }

    public static function createServer(Orders $order)
    {
        foreach ($order->products()->get() as $product2) {
            $product = Products::findOrFail($product2->product_id);
            if (!isset($product->server_id)) {
                return;
            }
            $extension = Extensions::where('id', $product->server_id)->first();
            if (!$extension) {
                return false;
            }
            include_once(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php');
            $settings = $product->settings()->get();
            $config = [];
            foreach ($settings as $setting) {
                $config[$setting->name] = $setting->value;
            }
            $config['config_id'] = $product->id;
            foreach ($product2->config()->get() as $config2) {
                $config["config"][$config2->key] = $config2->value;
            }
            $user = User::findOrFail($order->client);
            $function = $extension->name . '_createServer';
            $function($user, $config, $order);
            return true;
        }
    }

    public static function suspendServer(Orders $order)
    {
        foreach ($order->products()->get() as $product2) {
            $product = Products::findOrFail($product2->product_id);
            if (!isset($product->server_id)) {
                return;
            }
            $extension = Extensions::where('id', $product->server_id)->first();
            if (!$extension) {
                return false;
            }
            include_once(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php');
            $settings = $product->settings()->get();
            $config = [];
            foreach ($settings as $setting) {
                $config[$setting->name] = $setting->value;
            }
            $config['config_id'] = $product->id;
            foreach ($product2->config()->get() as $config2) {
                $config["config"][$config2->key] = $config2->value;
            }
            $user = User::findOrFail($order->client);
            $function = $extension->name . '_suspendServer';
            $function($user, $config, $order);
            return true;
        }
    }

    public static function unsuspendServer(Orders $order)
    {
        foreach ($order->products()->get() as $product2) {
            $product = Products::findOrFail($product2->product_id);
            if (!isset($product->server_id)) {
                return;
            }
            $extension = Extensions::where('id', $product->server_id)->first();
            if (!$extension) {
                return false;
            }
            include_once(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php');
            $settings = $product->settings()->get();
            $config = [];
            foreach ($settings as $setting) {
                $config[$setting->name] = $setting->value;
            }
            $config['config_id'] = $product->id;
            foreach ($product2->config()->get() as $config2) {
                $config["config"][$config2->key] = $config2->value;
            }
            $user = User::findOrFail($order->client);
            $function = $extension->name . '_unsuspendServer';
            $function($user, $config, $order);
            return true;
        }
    }

    public static function terminateServer(Orders $order)
    {
        foreach ($order->products()->get() as $product2) {
            $product = Products::findOrFail($product2->product_id);
            if (!isset($product->server_id)) {
                return;
            }
            $extension = Extensions::where('id', $product->server_id)->first();
            if (!$extension) {
                return false;
            }
            include_once(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php');
            $settings = $product->settings()->get();
            $config = [];
            foreach ($settings as $setting) {
                $config[$setting->name] = $setting->value;
            }
            $config['config_id'] = $product->id;
            foreach ($product2->config()->get() as $config2) {
                $config["config"][$config2->key] = $config2->value;
            }
            $user = User::findOrFail($order->client);
            $function = $extension->name . '_terminateServer';
            $function($user, $config, $order);
            return true;
        }
    }
}
