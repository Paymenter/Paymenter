<?php

namespace App\Http\Controllers\Clients;

use App\Classes\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class APIController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tokens = $user->tokens()->get();
        $permissions = API::$permissions;

        if ($user->role_id !== 2) {
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
        if ($user->role_id == 2) {
            $permissions = array_diff($permissions, API::$adminPermissions);
        } else {
            foreach ($permissions as $permission) {
                if(!Str::startsWith($permission, 'admin:')) {
                    continue;
                }
                if (!API::hasPermission($user, $permission)) {
                    return redirect('/api')->with('error', 'You do not have permission to create API tokens with the permission "' . $permission . '".');
                }
            }
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
