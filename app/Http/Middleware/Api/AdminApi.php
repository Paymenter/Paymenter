<?php

namespace App\Http\Middleware\Api;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminApi
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validate bearers token
        if (!$request->bearerToken()) {
            return response()->json(['error' => 'The request is missing a valid bearer token.'], 401);
        }

        $token = ApiKey::where('token', hash('sha256', $request->bearerToken()))
            ->where('enabled', true)
            ->firstOr(function () {
                abort(401, 'The provided API key is invalid or has been disabled.');
            });

        // Check if the token is of type 'admin'
        if ($token->type !== 'admin' || ($token->ip_addresses && !in_array($request->ip(), $token->ip_addresses))) {
            abort(403, 'You do not have permission to access this resource.');
        }

        $token->last_used_at = now();
        $token->save();

        // Attach the token to the request for further use
        $request->attributes->set('api_key', $token);
        $request->attributes->set('api_key_permissions', $token->permissions);

        return $next($request);
    }
}
