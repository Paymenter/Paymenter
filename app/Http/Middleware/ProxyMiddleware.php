<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProxyMiddleware
{
    protected $headers = Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_PREFIX |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (count(config('settings.trusted_proxies', [])) > 0) {
            if (in_array('*', config('settings.trusted_proxies'))) {
                $request->setTrustedProxies([$request->server->get('REMOTE_ADDR')], $this->headers);
            } else {
                $request->setTrustedProxies(config('settings.trusted_proxies'), $this->headers);
            }
        }

        return $next($request);
    }
}
