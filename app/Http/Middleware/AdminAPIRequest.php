<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class AdminAPIRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, \Closure $next)
    {
        $user = auth('sanctum')->user();

        if ($user->is_admin > 0) {
            if (auth()->user()->permissions != null || auth()->user()->permissions != []) {
                return redirect()->route('login')->with('error', 'Only Admins with full permissions can use the Admin API.');
            }

            return $next($request);
        }

        return redirect()->route('login');
    }
}
