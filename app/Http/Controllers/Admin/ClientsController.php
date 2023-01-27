<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\{Invoices, Orders, Tickets};
use Illuminate\Support\Facades\Route;

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
        $routeCollection = Route::getRoutes();
        $permissions = [];
        foreach ($routeCollection as $value) {
            // If route is admin route, add to permissions array
            if(strpos($value->getName(), 'admin.') !== false) {
                $permissions[] = $value->getName();
            }
        }
        return view('admin.clients.edit', compact('user', 'permissions'));
    }
    public function loginasClient(User $id)
    {
        $user = $id;
        auth()->login($user);
        return redirect()->route('index');
    }

    public function update(Request $request, User $id)
    {
        if(auth()->user()->id == $id->id) {
            return redirect()->back()->with('error', 'You cannot edit your own account');
        }
        if(auth()->user()->permissions){
            return redirect()->back()->with('error', 'Only Admins with full permissions can edit users');
        }
        $user = $id;
        $user->update($request->all());
        if($request->admin){
            $user->is_admin = 1;
            if($request->permissions){
                $user->permissions = $request->permissions;
            } else {
                $user->permissions = [];
            }
            $user->save();
        }else{
            $user->is_admin = 0;
            $user->permissions = [];
            $user->save();
        }
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