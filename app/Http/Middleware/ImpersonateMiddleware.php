<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ImpersonateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('impersonating')) {
            if ($request->is('admin/*')) {
                // Unset session
                session()->forget('impersonating');
            } else {
                Auth::onceUsingId(session('impersonating'));
            }
        }

        return $next($request);
    }
}
