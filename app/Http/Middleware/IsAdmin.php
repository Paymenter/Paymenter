<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
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
        if (Auth::user() && Auth::user()->is_admin == 1) {
            $user = Auth()->user();
            if (!$user->permissions) {
                return $next($request);
            }
            // Check if user has permission to access this page
            if ($user->has($request->route()->getName())) {
                return $next($request);
            }

            return redirect()->back()->with('error', 'You do not have permission to access this page');
        }

        return redirect()->route('index');
    }
}
