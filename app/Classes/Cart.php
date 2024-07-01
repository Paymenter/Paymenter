<?php

namespace App\Classes;

class Cart
{
    public static function get()
    {
        return collect(session('cart', []));
    }

    public static function add($product, $plan, $configOptions, Price $total)
    {
        $cart = self::get()->push([
            'product' => $product,
            'plan' => $plan,
            'configOptions' => $configOptions,
            'price' => $total,
        ]);

        session(['cart' => $cart]);

        // Return index of the newly added item
        return $cart->count() - 1;
    }

    public static function remove($index)
    {
        $cart = self::get();
        $cart->forget($index);
        session(['cart' => $cart]);
    }
}
