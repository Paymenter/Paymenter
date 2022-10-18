<?php
namespace App\Helpers;
use App\Models\Orders;
use App\Models\Products;
use App\Models\User;
use App\Models\Extensions;
use Illuminate\Http\Request;

class ExtensionHelper
{
    /**
     * Called when a new order is accepted
     * @return void
     */
    static function paymentDone($id)
    {
        $order = Orders::findOrFail($id);
        $order->status = 'paid';
        $order->save();
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

    public static function getProducts(){

        $products = session()->get('cart');
        // cast to array
        return $products;
    }
}
