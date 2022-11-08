<?php
namespace App\Http\Controllers;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class HomeController extends Controller
{
    function index()
    {
        $services = Orders::where('client', auth()->user()->id)->get();
        return view('home', compact('services'));
    }

    function manifest(Request $request)
    {
        $json = json_encode($request->input(), JSON_UNESCAPED_SLASHES);
        echo $json;
        return;
    }

    function profile(Request $request)
    {
        $users = User::all();
        return view('profile', compact('users', $request));
    }

    function password()
    {
        return view('layouts.password');
    }

    function update(Request $request)
    {
        $user = Auth::user();
        $user->update($request->all());
        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}