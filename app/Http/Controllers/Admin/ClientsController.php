<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.clients.index', compact('users'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        $user = User::create($request->all());
        return redirect()->route('admin.clients.edit', $user->id);
    }

    public function edit(User $id)
    {
        $user = $id;
        return view('admin.clients.edit', compact('user'));
    }

    public function update(Request $request, User $id)
    {
        $user = $id;
        $user->update($request->all());
        return redirect()->route('admin.clients.edit', $id)->with('success', 'User updated successfully');
    }

    public function destroy(User $id)
    {
        $user = $id;
        $user->delete();
        return redirect()->route('admin.clients');
    }
}