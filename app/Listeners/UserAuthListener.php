<?php

namespace App\Listeners;

use App\Classes\Cart;
use App\Events\Auth\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cookie;

class UserAuthListener
{
    /**
     * Handle the event.
     */
    public function handle(Login|Logout $event): void
    {
        if ($event instanceof Login) {
            // Does request have cart?
            if (Cookie::has('cart')) {
                // Merge cart with user
                $cart = Cart::getOnce();
                if ($cart->exists) {
                    $cart->user_id = $event->user->id;
                    $cart->save();
                }
            } elseif ($event->user->cart) {
                // Set cart to user
                Cookie::queue('cart', $event->user->cart->ulid, 60 * 24 * 30); // 30 days
            }
        } elseif ($event instanceof Logout) {
            // Does request have cart?
            if (Cookie::has('cart')) {
                // Remove cart from user
                Cookie::queue(Cookie::forget('cart'));
            }
        }
    }
}
