<?php

namespace App\Actions\Auth;

use App\Events\Auth\Login as LoginEvent;
use App\Models\User;
use App\Models\UserSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class Login
{
    public function execute(User $user, bool $remember = false): void
    {
        Session::regenerate();

        // Create user session token - middleware will handle actual login
        $userSession = UserSession::create([
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 512),
            'last_activity' => Carbon::now(),
            'expires_at' => $remember ? Carbon::now()->addDays(30) : null,
        ]);

        Session::put('user_session', $userSession->ulid);

        Auth::login($user);

        if ($remember) {
            Cookie::queue('paymenter_remember', $userSession->ulid, 60 * 24 * 30); // 30 days
        }

        event(new LoginEvent($user));
    }
}
