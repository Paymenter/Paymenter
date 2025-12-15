<?php

namespace App\Livewire\Auth;

use App\Livewire\Component;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class Logout extends Component
{
    public function logout()
    {
        // Delete the UserSession token to prevent reuse
        if (Session::has('user_session')) {
            $token = UserSession::firstWhere('ulid', Session::get('user_session'));
            if ($token) {
                $token->delete();
            }
        }

        // Clear session and cookie
        Session::invalidate();
        Session::regenerateToken();
        Cookie::queue(Cookie::forget('paymenter_remember'));

        Auth::logout();

        return redirect('/');
    }

    public function render()
    {
        return view('auth.logout');
    }
}
