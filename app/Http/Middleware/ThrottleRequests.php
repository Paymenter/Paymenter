<?php

namespace App\Http\Middleware;

use App\Support\ClientIp;
use Illuminate\Routing\Middleware\ThrottleRequests as BaseThrottleRequests;

/**
 * Laravel's throttle middleware, counting a guest's IPv6 /64 as one address.
 */
class ThrottleRequests extends BaseThrottleRequests
{
    protected function resolveRequestSignature($request)
    {
        if (!$request->user() && ($route = $request->route())) {
            $value = $route->getDomain() . '|' . ClientIp::key($request->ip());

            return self::$shouldHashKeys ? sha1($value) : $value;
        }

        return parent::resolveRequestSignature($request);
    }
}
