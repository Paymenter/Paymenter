<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

abstract class ApiController extends Controller
{
    protected function allowedIncludes($includes = []): array
    {
        // Check if user has permission to include the specified relationships
        $allowedIncludes = [];

        foreach ($includes as $include) {
            // Ensure the include ends with 's', the permissions are defined in plural form but the relation might be singular
            $relation = str_ends_with($include, 's') ? $include : $include . 's';

            if (
                Auth::user()->tokenCan("admin.{$relation}.viewAny") &&
                Auth::user()->hasPermission("admin.{$relation}.viewAny")
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
