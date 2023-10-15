<?php

namespace App\Http\Controllers\API\Clients;

use App\Classes\API;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Controller;

class APIController extends Controller
{
    /**
     * Get all available permissions.
     */
    public function getPermissions()
    {
        return $this->success('Success', [
            'permissions' => API::$permissions,
        ]);
    }

    /**
     * Create API token.
     */
    public function createAPIToken(Request $request)
    {
        $user = $request->user();
        $body = $request->json()->all();

        return response()->json([
            'token' => $user->createToken($body['tokenName'], $body['permissions'])->plainTextToken,
        ], 201);
    }


    /**
     * Get authenticated user.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMe(Request $request)
    {
        $user = $request->user();

        return $this->success('Success', [
            'user' => $user,
        ]);
    }
}
