<?php

namespace App\Http\Middleware;

use App\Classes\Cart;
use App\Exceptions\DisplayException;
use App\Models\Currency;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckoutParameterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->handleCurrencySwitch($request);
        $this->handleCouponApplication($request);

        return $next($request);
    }

    /**
     * Handle currency switching if requested
     */
    private function handleCurrencySwitch(Request $request): void
    {
        if (!$request->has('currency')) {
            return;
        }

        $currencyCode = $request->input('currency');

        // Don't allow currency changes if cart has items or currency doesn't exist
        if ($this->shouldBlockCurrencyChange($currencyCode)) {
            return;
        }

        session(['currency' => $currencyCode]);
    }

    /**
     * Handle coupon application if requested
     */
    private function handleCouponApplication(Request $request): void
    {
        if (!$request->has('coupon')) {
            return;
        }

        try {
            Cart::applyCoupon($request->input('coupon'));
        } catch (DisplayException $e) {
            return;
        }
    }

    /**
     * Determine if currency change should be blocked
     */
    private function shouldBlockCurrencyChange(string $currencyCode): bool
    {
        return Cart::items()->count() > 0 ||
            Currency::where('code', $currencyCode)->doesntExist();
    }
}
