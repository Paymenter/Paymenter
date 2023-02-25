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

        return view('clients.api', compact('tokens', 'permissions'));
    }
    
    public function create(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'required|string',
            'permissions' => 'required|array',
        ]);

        $token = $user->createToken(request('name'), array_keys(request('permissions')))->plainTextToken;
        
        return redirect('/api')->with('success', 'Here is your API token: "' . $token . '" Please save it somewhere safe. You will not be able to see it again.');
    }
    
    public function delete(Request $request, string $id)
    {
        $user = $request->user();
        $user->tokens()->where('id', $id)->delete();
        
        return redirect('/api')->with('success', 'API token deleted.');
    }
}
