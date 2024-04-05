<?php

namespace App\Http\Controllers\Clients;

use App\Helpers\ExtensionHelper;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Extension;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use RobThree\Auth\TwoFactorAuth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $services = $user->orders()->with(['products', 'products.product'])->get();
        $invoices = Invoice::where('user_id', $user->id)->where('credits', null)->where('status', 'pending')->with(['items.product.order.coupon', 'items.product.product'])->get();

        return view('clients.home', compact('services', 'invoices'));
    }

    /**
     * Show profile page with 2 factor authentication code if not enabled
     *
     * @param Request $request
     * @return Illuminate\View\View
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

    /**
     * Destroy all sessions except current
     *
     * @param Request $request
     * @return void
     */
    public function destroySessions(Request $request)
    {
        $user = $request->user();
        $sessions = $user->sessions()->where('id', '!=', $request->session()->getId())->get();
        foreach ($sessions as $session) {
            $session->delete();
        }

        return redirect()->back()->with('success', 'Sessions destroyed successfully');
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
        $countries = \App\Classes\Constants::countries();
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => (config('settings::requiredClientDetails_address') == 1 ? 'required|': 'nullable|') . 'string',
            'city' => (config('settings::requiredClientDetails_city') == 1 ? 'required|': 'nullable|') . 'string',
            'country' => (config('settings::requiredClientDetails_country') == 1 ? 'required|': 'nullable|') . 'string|in:' . implode(',', array_keys($countries)),
            'phone' => (config('settings::requiredClientDetails_phone') == 1 ? 'required|': 'nullable|') . 'string',
            'zip' => (config('settings::requiredClientDetails_zip') == 1 ? 'required|': 'nullable|') . 'string',
        ]);

        $user = $request->user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->country = $request->country;
        $user->phone = $request->phone;
        $user->zip = $request->zip;
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
        if (config('settings::credits') == false) {
            return abort(404, 'Credits are disabled');
        }
        $gateways = ExtensionHelper::getGateways();
        $userInvoices = Invoice::where('user_id', Auth::user()->id)->with(['items.product.order.coupon', 'items.product.product'])->get();
        return view('clients.credits', compact('gateways', 'userInvoices'));
    }

    /**
     * Add credit to user
     *
     * @param Request $request
     * @return redirect
     */
    public function addCredits(Request $request)
    {
        if (!config('settings::credits')) {
            return redirect()->back()->with('error', 'Credits are disabled');
        }
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'gateway' => 'required|string',
        ]);

        $gateway = Extension::findOrFail($request->gateway);
        $amount = $request->amount;
        if ($amount < config('settings::minimum_deposit')) {
            return redirect()->back()->with('error', 'Minimum deposit is ' . config('settings::minimum_deposit'));
        }
        if ($amount > config('settings::maximum_deposit')) {
            return redirect()->back()->with('error', 'Maximum deposit is ' . config('settings::maximum_deposit'));
        }
        $user = $request->user();
        if (($user->credits + $amount) > config('settings::maximum_balance')) {
            return redirect()->back()->with('error', 'Maximum credits is ' . config('settings::maximum_credits'));
        }

        // Make invoice for user
        $invoice = new Invoice();
        $invoice->user_id = $user->id;
        $invoice->credits = $amount;
        $invoice->status = 'pending';
        $invoice->save();

        return redirect(ExtensionHelper::addCredits($gateway, $invoice));
    }


    public function affiliate()
    {
        if (!config('settings::affiliate')) {
            abort(404);
        }
        $affiliate = Auth::user()->affiliate()->with('affiliateUsers')->get()->first();
        return view('clients.affiliate', compact('affiliate'));
    }

    /**
     * Store affiliate
     *
     * @param Request $request
     * @return void
     */
    public function affiliateStore(Request $request)
    {
        if (!config('settings::affiliate')) {
            abort(404);
        }
        $user = $request->user();
        $affiliate = $user->affiliate;
        if ($affiliate) {
            return redirect()->back()->with('error', 'You already have an affiliate');
        }
        if (config('settings::affiliate_type') == 'custom') {
            $request->validate([
                'code' => 'required|unique:affiliates,code',
            ]);
        };
        $affiliate = new Affiliate();
        $affiliate->user()->associate($user);
        if (config('settings::affiliate_type') == 'custom') {
            $affiliate->code = $request->code;
        } else if(config('settings::affiliate_type') == 'random') {
            $affiliate->code = Str::random(10);
        } else if(config('settings::affiliate_type') == 'fixed') {
            $affiliate->code = str_replace(' ', '', $user->name);
        }
        $affiliate->save();

        return redirect()->back()->with('success', 'Affiliate created successfully');
    }
}
