<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get information about current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => $user,
        ], 200);
    }
}
