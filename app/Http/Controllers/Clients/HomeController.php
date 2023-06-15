<?php

namespace App\Http\Controllers\Clients;

use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use RobThree\Auth\TwoFactorAuth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $services = Order::where('client', $user->id)->get();
        $invoices = Invoice::where('user_id', $user->id)->where('status', 'pending')->get();

        return view('clients.home', compact('services', 'invoices'));
    }

    /**
     * Show profile page with 2 factor authentication code if not enabled
     *
     * @param Request $request
     * @return void
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $tfa = new TwoFactorAuth();

        if (!$user->tfa_secret) {
            $secret = $tfa->createSecret();
            $qr = $tfa->getQRCodeImageAsDataUri(config('app.name', 'Paymenter') . '-' . $user->email, $secret);

            return view('clients.profile', compact('secret', 'qr'));
        }

        return view('clients.profile');
    }

    /**
     * Update 2 factor authentication
     *
     * @param Request $request
     * @return void
     */
    public function update2FA(Request $request)
    {
        $user = $request->user();

        if ($request->has('disable')) {
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

    /**
     * Update user
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'phone' => 'required|numeric',
        ]);
        
        $user = $request->user();
        $user->name = $request->name;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->country = $request->country;
        $user->phone = $request->phone;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    /**
     * Add credit to user
     * 
     * @return view
     */
    public function credits()
    {
        return view('clients.credits');
    }
}
