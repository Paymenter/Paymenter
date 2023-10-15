<?php

namespace App\Http\Controllers\API\Admin;

use App\Classes\API;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Controller;

class APIController extends Controller
{
    /**
     * Get all available permissions.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissions()
    {
        return response()->json([
            'permissions' => API::$adminPermissions,
        ], 200);
    }
    
    /**
     * Create API token.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAPIToken(Request $request)
    {
        $user = $request->user();
        $body = $request->json()->all();

        if (!$user->tokenCan('admin:api:create')) {
            return response()->json([
                'error' => 'You do not have permission to create API tokens.',
            ], 403);
        }

        return response()->json([
            'token' => $user->createToken($body['tokenName'], $body['permissions'])->plainTextToken,
        ], 201);
    }
}
