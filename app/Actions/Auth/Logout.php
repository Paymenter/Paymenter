<?php

namespace App\Actions\Auth;

use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class Logout
{
    public function execute(): void
    {
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
    }
}
