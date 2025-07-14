<?php

namespace Paymenter\Extensions\Others\Affiliates\Middleware;

use App\Helpers\ExtensionHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;
use Symfony\Component\HttpFoundation\Response;

class AffiliatesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Continue if request doesn't contain `ref` query parameter
        if (!request()->has('ref')) {
            return $next($request);
        }

        $affiliate = Affiliate::where('code', request('ref'))->first();

        if (!$affiliate || $affiliate->user->id === auth()->id()) {
            return $next($request);
        }

        // Increase the visitor count if a cookie is not present already, or is not the same one
        if (!Cookie::has('referred_by') || Cookie::get('referred_by') !== $affiliate->code) {
            $affiliate->increment('visitors');
        }

        $extension = ExtensionHelper::getExtension('other', 'Affiliates');
        $cookie_max_age = (int) $extension->config('cookie_max_age');

        if ($cookie_max_age > 0) {
            // Set affiliate cookie (and set it to expire after `$cookie_max_age` days)
            Cookie::queue('referred_by', $affiliate->code, minutes: 60 * 24 * $cookie_max_age);
        } else {
            // Never expire
            Cookie::queue(Cookie::forever('referred_by', $affiliate->code));
        }

        return $next($request);
    }
}
