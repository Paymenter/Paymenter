<?php

namespace App\Http\Controllers\Clients;

use App\Models\User;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use RobThree\Auth\TwoFactorAuth;

class HomeController extends Controller
{
    public function index()
    {
        $services = Order::where('client', auth()->user()->id)->get();
        $invoices = Invoice::where('user_id', auth()->user()->id)->where('status', 'pending')->get();

        return view('clients.home', compact('services', 'invoices'));
    }

    public function profile()
    {
        $tfa = new TwoFactorAuth();
        if (!auth()->user()->tfa_secret) {
            $secret = $tfa->createSecret();
            $qr = $tfa->getQRCodeImageAsDataUri(config('app.name', 'Paymenter') . '-' . auth()->user()->email, $secret);
            return view('clients.profile', compact('secret', 'qr'));
        }
        return view('clients.profile');
    }

    public function tfa(Request $request)
    {
        if ($request->has('disable')) {
            $user = User::find(auth()->user()->id);
            if (!password_verify($request->password, $user->password)) {
                return redirect()->back()->with('error', 'Invalid password');
            }
            $user->tfa_secret = null;
            $user->save();
            return redirect()->back()->with('success', 'Two factor authentication disabled');
        }
        $request->validate([
            'secret' => 'required|size:16',
            'code' => 'required|size:6',
            'password' => 'required',
        ]);
        $user = User::find(auth()->user()->id);
        if (!password_verify($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid password');
        }
        $tfa = new TwoFactorAuth();
        $secret = $request->secret;
        $code = $request->code;
        $valid = $tfa->verifyCode($secret, $code);
        if ($valid) {
            $user->tfa_secret = Crypt::encrypt($secret);
            $user->save();
            return redirect()->back()->with('success', 'Two factor authentication enabled');
        }
        return redirect()->back()->with('error', 'Invalid code');
    }

    public function password()
    {
        return view('auth.passwords.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'country' => 'required',
            'phone' => 'required',
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
