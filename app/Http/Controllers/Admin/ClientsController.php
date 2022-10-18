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
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        return redirect()->route('admin.clients.index');
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.clients.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        return redirect()->route('admin.clients.index');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('admin.clients.index');
    }
}