<?php

use App\Models\Orders;
use App\Models\Products;
use App\Models\User;
use App\Models\Extensions;

class Extension
{
    function paymentDone($id)
    {
        $order = Orders::find($id);
        $order->status = 'paid';
        $order->save();
    }

    function paymentFailed($id)
    {
        $order = Orders::find($id);
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
            ]);
            $extension = Extensions::where('name', $name)->first();
        }
        error_log($extension);
        $config = $extension->getConfig()->where('key', $key)->first();
        return $config->value;
    }
}
