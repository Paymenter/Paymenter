<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use RobThree\Auth\TwoFactorAuth;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Validators\ReCaptcha;
use Illuminate\Support\Facades\Crypt;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        (new ReCaptcha())->verify($request);
        $request->authenticate();

        if (Auth::user()->tfa_secret) {
            $request->session()->put('auth_confirmation_token', [
                'user_id' => Auth::user()->id,
                'token_value' => $token = Str::random(64),
                'expires_at' => CarbonImmutable::now()->addMinutes(5),
                'remember' => $request->filled('remember'),
            ]);
            Auth::logout();

            return redirect()->route('tfa');
        }

        $request->session()->regenerate();

        return redirect(RouteServiceProvider::HOME);
    }

    /** 
     * Return view for 2FA
     * 
     * @return \Illuminate\View\View
     */
    public function twoFactorChallenge()
    {
        // Check if auth_confirmation_token exists
        if (!session()->has('auth_confirmation_token')) {
            return redirect()->route('login');
        }
        return view('auth.tfa');
    }

    /**
     * Handle 2FA
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function twoFactorAuthenticate(Request $request)
    {
        $tfa = new TwoFactorAuth(config('app.name'));

        $token = $request->session()->get('auth_confirmation_token');

        if (!$token) {
            return redirect()->route('login');
        }

        if (CarbonImmutable::now()->greaterThan($token['expires_at'])) {
            $request->session()->forget('auth_confirmation_token');
            return redirect()->route('login')->withErrors(['code' => 'The 2FA code you entered has expired.']);
        }

        $user = User::findOrFail($token['user_id']);

        if ($tfa->verifyCode(Crypt::decrypt($user->tfa_secret), $request->code, 2)) {
            Auth::loginUsingId($token['user_id'], $token['remember']);
            $request->session()->regenerate();
            $request->session()->forget('auth_confirmation_token');
            return redirect()->route('clients.home');
        } else {
            return back()->withErrors(['code' => 'The 2FA code you entered is incorrect.']);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate(
            [
                'current_password' => 'required',
                'new_password' => 'required',
                'new_password_confirmation' => 'required|same:new_password',
            ]
        );

        if ($request->new_password == $request->new_password_confirmation) {
            if (Auth::attempt(['email' => Auth::user()->email, 'password' => $request->current_password])) {
                $user = Auth::user();
                $user->password = bcrypt($request->new_password);
                DB::table('users')->where('id', $user->id)->update(['password' => $user->password]);

                return redirect()->route('clients.home')->with('success', 'Password changed successfully!');
            } else {
                return back()->withErrors(['current_password' => 'The current password you entered is incorrect.']);
            }
        } else {
            return back()->withErrors(['new_password' => 'The new password and new password confirmation do not match.']);
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
