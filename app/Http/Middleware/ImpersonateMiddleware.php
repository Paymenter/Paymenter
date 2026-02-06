<?php

namespace App\Http\Middleware;

use App\Models\User;
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
                $currentUser = Auth::user();

                // Only allow admins (users with a role) to impersonate
                if (!$currentUser || !$currentUser->role_id) {
                    session()->forget('impersonating');

                    return $next($request);
                }

                $targetUser = User::find(session('impersonating'));

                if (!$targetUser) {
                    session()->forget('impersonating');

                    return $next($request);
                }
                
                Auth::onceUsingId(session('impersonating'));
            }
        }

        return $next($request);
    }
}
