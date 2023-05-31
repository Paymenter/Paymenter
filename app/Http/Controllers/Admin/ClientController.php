<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Models\{Invoice, Order, Role, Ticket};
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
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
        $password = Hash::make($request->password);
        $request->merge(['password' => $password]);
        $user = User::create($request->all());

        return redirect()->route('admin.clients.edit', $user->id);
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.clients.edit', compact('user', 'roles'));
    }

    public function loginasClient(User $user)
    {
        auth()->login($user);

        return redirect()->route('index');
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->id == $user->id) {
            return redirect()->back()->with('error', 'You cannot edit your own account');
        }
        if (auth()->user()->permissions) {
            return redirect()->back()->with('error', 'Only Admins with full permissions can edit users');
        }
        $user->update($request->all());
        return redirect()->route('admin.clients.edit', $user->id)->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Delete tickets, orders, etc.
        $tickets = Ticket::where('client', $user->id)->get();
        foreach ($tickets as $ticket) {
            $ticket->delete();
        }
        $orders = Order::where('client', $user->id)->get();
        foreach ($orders as $order) {
            $order->delete();
        }
        $invoices = Invoice::where('user_id', $user->id)->get();
        foreach ($invoices as $invoice) {
            $invoice->delete();
        }
        $user->delete();

        return redirect()->route('admin.clients');
    }
}
