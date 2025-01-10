<?php

namespace Paymenter\Extensions\Others\Affiliates\Middleware;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;
use Symfony\Component\HttpFoundation\Response;

class AffiliatesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Continue if request doesn't contain `ref` query parameter
        if (Auth::check() || !request()->has('ref')) {
            return $next($request);
        }

        try {
            $affiliate = Affiliate::where('code', request('ref'))->firstOrFail();

            if (Auth::check()) {
                // User has already registered before
                return $next($request);
            }

            $affiliate->increment('visitors');

            // Set affiliate cookie
            Cookie::queue('referred_by', request('ref'), 60 * 24 * 90);
        } catch (ModelNotFoundException $e) {
            //
        }

        return $next($request);
    }
}
