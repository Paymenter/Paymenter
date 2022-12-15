<?php
namespace App\Http\Controllers\Clients;


use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Invoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class HomeController extends Controller
{
    function index()
    {
        $services = Orders::where('client', auth()->user()->id)->get();
        $invoices = Invoices::where('user_id', auth()->user()->id)->where('status', 'pending')->get();
        
        return view('clients.home', compact('services', 'invoices'));
    }

    function profile()
    {
        return view('clients.profile');
    }

    function password()
    {
        return view('auth.passwords.change-password');
    }

    function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'country' => 'required',
            'phone' => 'required'
        ]);
        $user = User::find(auth()->user()->id);
        $user->name = $request->name;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->country = $request->country;
        $user->phone = $request->phone;
        $user->save();
        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}