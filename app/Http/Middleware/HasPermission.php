<?php

namespace App\Http\Middleware;

use App\Utils\Permissions;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class HasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if(!Auth::check())
            return redirect()->route('login');
        $user = Auth::user();
        foreach ($permissions as $permission) {
            if ((new Permissions($user->role->permissions))->has($permission))
                return $next($request);
        }
        if ($request->expectsJson()) {
            return response(null, 403);
        }
        return redirect()->route('clients.home')->with('error', 'You do not have permission to access this page');
    }
}
