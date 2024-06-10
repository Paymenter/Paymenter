<?php

namespace App\Classes;


class Cart
{
    public static function get()
    {
        return collect(session('cart', []));
    }

    public static function add($product)
    {
        $cart = self::get();

        $cart->push([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
        ]);

        session(['cart' => $cart]);
    }
}
