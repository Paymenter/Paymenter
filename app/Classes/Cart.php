<?php

namespace App\Classes;

use App\Exceptions\DisplayException;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Cart
{
    public static function get()
    {
        return collect(session('cart', []));
    }

    public static function add($product, $plan, $configOptions, $checkoutConfig, Price $price, $quantity = 1, $key = null)
    {
        if (isset($key)) {
            $cart = self::get();
            // Check if key exists
            $cart[$key] = (object) [
                'product' => (object) $product,
                'plan' => (object) $plan,
                'configOptions' => (object) $configOptions,
                'checkoutConfig' => (object) $checkoutConfig,
                'price' => $price,
                'quantity' => $quantity,
            ];
        } else {
            $cart = self::get()->push((object) [
                'product' => (object) $product,
                'plan' => (object) $plan,
                'configOptions' => (object) $configOptions,
                'checkoutConfig' => (object) $checkoutConfig,
                'price' => $price,
                'quantity' => $quantity,
            ]);
        }

        session(['cart' => $cart]);

        if (Session::has('coupon')) {
            // Reapply coupon to the cart
            try {
                $coupon = Session::get('coupon');
                self::removeCoupon();
                self::applyCoupon($coupon->code);
            } catch (DisplayException $e) {
                // Ignore exception
            }
        }

        // Return index of the newly added item
        return $key ?? $cart->count() - 1;
    }

    public static function remove($index)
    {
        $cart = self::get();
        $cart->forget($index);
        session(['cart' => $cart]);
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

        return $coupon;
    }

    public static function applyCoupon($code)
    {
        $coupon = self::validateCoupon($code);

        $wasSuccessful = false;
        $items = self::get()->map(function ($item) use ($coupon, &$wasSuccessful) {
            if ($coupon->products->where('id', $item->product->id)->isEmpty() && $coupon->products->isNotEmpty()) {
                return (object) $item;
            }
            $wasSuccessful = true;
            $discount = 0;
            if ($coupon->type === 'percentage') {
                $discount = $item->price->price * $coupon->value / 100;
            } elseif ($coupon->type === 'fixed') {
                $discount = $coupon->value;
            } else {
                $discount = $item->price->setup_fee;
                $item->price->setup_fee = 0;
            }
            if ($item->price->price < $discount) {
                $discount = $item->price->price;
            }
            $item->price->setDiscount($discount);
            $item->price->price -= $discount;

            return (object) $item;
        });

        session(['cart' => $items]);
        Session::put(['coupon' => $coupon]);

        if ($wasSuccessful) {
            return $items;
        } else {
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
        if (!Session::has('coupon')) {
            return true;
        }

        try {
            $coupon = Session::get('coupon');
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
        Session::forget('coupon');
        $items = self::get()->map(function ($item) {
            $item->price = new Price([
                'price' => $item->price->original_price,
                'setup_fee' => $item->price->original_setup_fee,
                'currency' => $item->price->currency,
            ], apply_exclusive_tax: true);

            return (object) $item;
        });
        session(['cart' => $items]);
    }
}
