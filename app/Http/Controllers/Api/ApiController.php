<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

abstract class ApiController extends Controller
{
    const MAPPED_INCLUDES = [
        'messages' => 'ticket_messages',
        'role' => 'roles',
        'user' => 'users',
        'ticket' => 'tickets',
        'plans.prices' => 'products',
    ];

    protected function allowedIncludes($includes = []): array
    {
        // Check if user has permission to include the specified relationships
        $allowedIncludes = [];

        foreach ($includes as $include) {
            // Check if the include is mapped to a specific relation
            $relation = self::MAPPED_INCLUDES[$include] ?? $include;

            if (
                in_array('admin.' . $relation . '.view', request()->attributes->get('api_key_permissions', [])) ||
                !in_array('admin.' . $relation . '.view', config('permissions.api.admin', []))
            ) {
                $allowedIncludes[] = $include;
            }
        }

        return $allowedIncludes;
    }

    /**
     * Return an HTTP/204 response for the API.
     */
    protected function returnNoContent(): Response
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
