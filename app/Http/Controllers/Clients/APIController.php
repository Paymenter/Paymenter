<?php

namespace App\Http\Controllers\Clients;

use App\Classes\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tokens = $user->tokens()->get();
        $permissions = API::$permissions;

        if ($user->is_admin == 1) {
            $permissions = array_merge($permissions, API::$adminPermissions);
        }

        return view('clients.api', compact('tokens', 'permissions'));
    }
    
    public function create(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'required|string',
            'permissions' => 'required|array',
        ]);

        $permissions = array_keys(request('permissions'));

        // Prevent admin permissions from being added by non-admins
        if ($user->is_admin == 0) {
            $permissions = array_diff($permissions, API::$adminPermissions);
        }

        $token = $user->createToken(request('name'), $permissions)->plainTextToken;
        
        return redirect('/api')->with('success', 'Here is your API token: "' . $token . '" Please save it somewhere safe. You will not be able to see it again.');
    }
    
    public function delete(Request $request, string $id)
    {
        $user = $request->user();
        $user->tokens()->where('id', $id)->delete();
        
        return redirect('/api')->with('success', 'API token deleted.');
    }
}
