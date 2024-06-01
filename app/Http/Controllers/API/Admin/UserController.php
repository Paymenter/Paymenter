<?php

namespace App\Http\Controllers\API\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\API\Controller;
use App\Models\User;

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

    /**
     * Get credits for a user.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCredits(Request $request, int $userId)
    {
        $user = User::findOrFail($userId);

        return response()->json([
            'credits' => $user->credits,
        ], 200);
    }

    /**
     * Set credits for a user.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function setCredits(Request $request, int $userId)
    {
        $request->validate([
            'credits' => 'required|integer',
        ]);

        $user = User::findOrFail($userId);

        $user->credits = $request->input('credits');
        $user->save();

        return response()->json([
            'credits' => $user->credits,
        ], 200);
    }
}
