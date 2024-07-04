<?php

namespace App\Classes;

class Cart
{
    public static function get()
    {
        return collect(session('cart', []));
    }

    public static function add($product, $plan, $configOptions, Price $price, $quantity = 1, $key = null)
    {
        if (isset($key)) {
            $cart = self::get();
            // Check if key exists
            $cart[$key] = (object) [
                'product' => (object) $product,
                'plan' => (object) $plan,
                'configOptions' => (object) $configOptions,
                'price' => $price,
                'quantity' => $quantity,
            ];
        } else {
            $cart = self::get()->push((object) [
                'product' => (object) $product,
                'plan' => (object) $plan,
                'configOptions' => (object) $configOptions,
                'price' => $price,
                'quantity' => $quantity,
            ]);
        }

        session(['cart' => $cart]);

        // Return index of the newly added item
        return $key ?? $cart->count() - 1;
    }

    public static function remove($index)
    {
        $cart = self::get();
        $cart->forget($index);
        session(['cart' => $cart]);
    }
}
