<?php
namespace App\Helpers;
use App\Models\Orders;
use App\Models\Products;
use App\Models\User;
use App\Models\Invoices;
use App\Models\Extensions;
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
        if(!$config){
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
        if(!$config){
            $extension->getConfig()->create([
                'key' => $key,
                'value' => $value,
            ]);
        }else{
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
        if(!$config){
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

    public static function getPaymentMethod($id, $total, $products, $orderId)
    {
        $extension = Extensions::where('id', $id)->first();
        if (!$extension) {
           return false;
        }
        // Get the payment method
        include_once(app_path() . '/Extensions/Gateways/' . $extension->name . '/index.php');
        // Set funciton name
        $function = $extension->name . '_pay';
        // Call the function
        $pay = $function($total, $products, $orderId);

        return $pay->url;
    }

    public static function createServer(Orders $order)
    {
        error_log('Creating server');
        foreach($order->products as $product){
            $product = Products::findOrFail($product['id']);
            $extension = Extensions::where('id', $product->server_id)->first();
            if (!$extension) {
                return false;
            }
            include_once(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php');
            $settings = $product->settings()->get();
            $config = [];
            foreach($settings as $setting){
                $config[$setting->name] = $setting->value;
            }
            $user = User::findOrFail($order->client);
            createServer($user, $config, $order);
            return true;
        }
    }

    public static function suspendServer(Orders $order)
    {
        foreach($order->products as $product){
            $product = Products::findOrFail($product['id']);
            $extension = Extensions::where('id', $product->server_id)->first();
            if (!$extension) {
                return false;
            }
            include_once(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php');
            suspendServer($order);
            return true;
        }
    }

    public static function unsuspendServer(Orders $order)
    {
        foreach($order->products as $product){
            $product = Products::findOrFail($product['id']);
            $extension = Extensions::where('id', $product->server_id)->first();
            if (!$extension) {
                return false;
            }
            include_once(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php');
            unsuspendServer($order);
            return true;
        }
    }

    public static function terminateServer(Orders $order)
    {
        foreach($order->products as $product){
            $product = Products::findOrFail($product['id']);
            $extension = Extensions::where('id', $product->server_id)->first();
            if (!$extension) {
                return false;
            }
            include_once(app_path() . '/Extensions/Servers/' . $extension->name . '/index.php');
            terminateServer($order);
            return true;
        }
    }

}
