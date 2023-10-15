<?php

namespace App\Http\Middleware;

use App\Classes\API;
use Closure;
use Illuminate\Http\Request;

class HasApiPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = $request->user();

        if (!$user->tokenCan($permission)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to ' . $permission . '.',
            ], 403);
        }

        // Additional check if the user role has also the api permission
        if (strpos($request->route()->uri, 'api/admin') !== false) {
            if (!API::hasPermission($user, $permission)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to ' . $permission . '.',
                ], 403);
            }
        }

        return $next($request);
    }
}
