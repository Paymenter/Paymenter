<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\{Invoices, Orders, Tickets};

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
    public function loginasClient(User $id)
    {
        $user = $id;
        auth()->login($user);
        return redirect()->route('index');
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
        // Delete tickets, orders, etc.
        $tickets = Tickets::where('client', $user->id)->get();
        foreach ($tickets as $ticket) {
            $ticket->delete();
        }
        $orders = Orders::where('client', $user->id)->get();
        foreach ($orders as $order) {
            $order->delete();
        }
        $invoices = Invoices::where('user_id', $user->id)->get();
        foreach ($invoices as $invoice) {
            $invoice->delete();
        }
        $user->delete();
        return redirect()->route('admin.clients');
    }
}