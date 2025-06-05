<?php

namespace App\Http\Middleware\Api;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AdminApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validate bearers token
        if (!$request->bearerToken()) {
            throw new UnauthorizedHttpException(
                'Bearer',
                'The request is missing a valid bearer token.'
            );
        }

        $token = ApiKey::where('token', hash('sha256', $request->bearerToken()))
            ->where('enabled', true)
            ->firstOr(function () {
                throw new UnauthorizedException(
                    'The provided API key is invalid or has been disabled.'
                );
            });

        // Check if the token is of type 'admin'
        if ($token->type !== 'admin' || ($token->ip_addresses && !in_array($request->ip(), $token->ip_addresses))) {
            throw new UnauthorizedException(
                'You do not have permission to access this resource.'
            );
        }

        // Optionally, you can log the last used time or IP address
        $token->last_used_at = now();
        $token->save();

        // Attach the token to the request for further use
        $request->attributes->set('api_key', $token);
        $request->attributes->set('api_key_permissions', $token->permissions);

        return $next($request);
    }
}
