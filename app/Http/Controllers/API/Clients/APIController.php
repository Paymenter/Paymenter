<?php

namespace App\Http\Controllers\API\Clients;

use App\Classes\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class APIController extends Controller
{
    /**
     * Get all available permissions.
     */
    public function getPermissions()
    {
        return response()->json([
            'permissions' => API::$permissions,
        ], 200);
    }
    
    /**
     * Create API token.
     */
    public function createAPIToken(Request $request)
    {
        $user = $request->user();
        $body = $request->json()->all();

        if (!$user->tokenCan('api:create')) {
            return response()->json([
                'error' => 'You do not have permission to create API tokens.',
            ], 403);
        }

        return response()->json([
            'token' => $user->createToken($body['tokenName'], $body['permissions'])->plainTextToken,
        ], 201);
    }
}
