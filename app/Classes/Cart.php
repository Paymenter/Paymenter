<?php

namespace App\Classes;

use App\Exceptions\DisplayException;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class Cart
{
    public static function getOnce()
    {
        if (!Cookie::has('cart') || !$cart = \App\Models\Cart::where('ulid', Cookie::get('cart'))->first()) {
            return new \App\Models\Cart;
        }

        return $cart->load('items.plan', 'items.product', 'items.product.configOptions.children.plans.prices');
    }

    public static function get()
    {
        return once(fn () => self::getOnce());
    }

    public static function clear()
    {
        if (Cookie::has('cart')) {
            \App\Models\Cart::where('ulid', Cookie::get('cart'))->delete();
            Cookie::queue(Cookie::forget('cart'));
        }
    }

    public static function items()
    {
        return self::get()->items;
    }

    public static function createCart()
    {
        $cart = self::getOnce();
        if (!$cart->exists) {
            $cart->user_id = Auth::id();
            $cart->currency_code = session('currency', session('currency', config('settings.default_currency')));
            $cart->save();
            Cookie::queue('cart', $cart->ulid, 60 * 24 * 30); // 30 days
            $cart = \App\Models\Cart::find($cart->id);
        }

        return $cart;
    }

    public static function add(Product $product, Plan $plan, $configOptions, $checkoutConfig, $quantity = 1, $key = null)
    {
        // Match on key
        $cart = self::createCart();

        $item = $cart->items()->updateOrCreate([
            'id' => $key,
        ], [
            'product_id' => $product->id,
            'plan_id' => $plan->id,
            'config_options' => $configOptions,
            'checkout_config' => $checkoutConfig,
            'quantity' => $quantity,
        ]);
        $cart->load('items.plan', 'items.product', 'items.product.configOptions.children.plans.prices');

        if ($cart->coupon_id) {
            // Reapply coupon to the cart
            try {
                self::validateCoupon($cart->coupon->code);
                // Check if any of the items have gotten a discount
                if ($cart->items->filter(fn ($item) => $item->price->hasDiscount())->isEmpty()) {
                    $cart->coupon_id = null;
                    $cart->save();
                }
            } catch (DisplayException $e) {
                // Coupon is invalid, remove it
                $cart->coupon_id = null;
                $cart->save();
            }
        }

        // Return index of the newly added item
        return $item->id;
    }

    public static function remove($index)
    {
        $cart = self::get();
        $item = $cart->items()->where('id', $index)->first();
        if ($item) {
            $item->delete(); // We also want to trigger Eloquent events
        }
        $cart->load('items.plan', 'items.product', 'items.product.configOptions.children.plans.prices');
    }

    public static function updateQuantity($index, $quantity)
    {
        $cart = self::get();
        if ($item = $cart->items()->where('id', $index)->first()) {
            if ($item->product->allow_quantity !== 'combined') {
                return;
            }
        } else {
            return;
        }

        if ($quantity < 1) {
            self::remove($index);

            return;
        }
        $item->quantity = $quantity;
        $item->save();

        $cart->load('items');
    }

    /**
     * Validate if a coupon is valid for the current user and cart
     *
     * @param  string  $coupon_code
     * @return Coupon
     *
     * @throws DisplayException
     */
    public static function validateCoupon($coupon_code)
    {
        $coupon = Coupon::where('code', $coupon_code)->first();

        if (!$coupon) {
            throw new DisplayException('Coupon code not found');
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            throw new DisplayException('Coupon code has expired');
        }
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            throw new DisplayException('Coupon code is not active yet');
        }
        if ($coupon->max_uses && $coupon->services->count() >= $coupon->max_uses) {
            throw new DisplayException('Coupon code has reached its maximum uses');
        }
        if (Auth::check() && $coupon->hasExceededMaxUsesPerUser(Auth::id())) {
            throw new DisplayException('You have already used this coupon the maximum number of times allowed');
        }
        if ($coupon->products->isNotEmpty()) {
            $cart = self::get();
            $applicable = false;
            foreach ($cart->items as $item) {
                if ($coupon->products->contains($item->product_id)) {
                    $applicable = true;
                    break;
                }
            }
            if (!$applicable) {
                throw new DisplayException('Coupon code is not valid for any items in your cart');
            }
        }

        return $coupon;
    }

    public static function applyCoupon($code)
    {
        $coupon = self::validateCoupon($code);

        $wasSuccessful = false;
        $cart = self::createCart();
        $cart->coupon_id = $coupon->id;
        $cart->save();

        // Check if any of the items have gotten a discount, if empty also set succesful because it's valid for future use (will get rechecked on checkout
        if ($cart->items->filter(fn ($item) => $item->price->hasDiscount())->isNotEmpty() || $cart->items->isEmpty()) {
            $wasSuccessful = true;
        } else {
            $cart->coupon_id = null;
        }

        if ($wasSuccessful) {
            $cart->save();

            return $cart;
        } else {
            $cart->coupon_id = null;
            $cart->save();
            throw new DisplayException('Coupon code is not valid for any items in your cart');
        }
    }

    /**
     * Validates and refreshes the coupon in the session
     *
     * @return bool True if coupon is valid, false otherwise
     */
    public static function validateAndRefreshCoupon()
    {
        if (!self::get()->coupon_id || !self::get()->coupon) {
            return true;
        }

        try {
            $coupon = self::get()->coupon;
            self::validateCoupon($coupon->code);

            return true;
        } catch (DisplayException $e) {
            // Coupon is invalid, remove it
            self::removeCoupon();

            return false;
        }
    }

    public static function removeCoupon()
    {
        self::get()->update(['coupon_id' => null]);
        self::get()->load('coupon');
    }
}
