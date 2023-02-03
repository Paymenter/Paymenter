<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

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
        $request->authenticate();

        $request->session()->regenerate();

        return redirect(RouteServiceProvider::HOME);
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
