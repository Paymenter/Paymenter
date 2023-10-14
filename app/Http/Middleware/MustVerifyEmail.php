<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class MustVerifyEmail
{
    public function handle(Request $request, \Closure $next)
    {
        if (!empty($request->route()->middleware())) {
            if (config('settings::must_verify_email') == 1 && in_array('auth', request()->route()->middleware())) {
                if (auth()->check() && !auth()->user()->hasVerifiedEmail() && !$request->routeIs('verification.notice') && !$request->routeIs('verification.verify') && !$request->routeIs('verification.send') && !$request->routeIs('logout')) {
                    return redirect()->route('verification.notice');
                }
            }
            return $next($request);
        }
        return $next($request);
    }
}
