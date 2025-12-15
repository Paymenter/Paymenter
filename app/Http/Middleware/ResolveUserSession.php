<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class ResolveUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $session = $request->session();
        $token = null;

        if ($session->has('user_session')) {
            $token = UserSession::findValid($session->get('user_session'));
        }

        if (!$token) {
            $id = Cookie::get('paymenter_remember');

            if ($id) {
                $token = UserSession::findValid($id);
            }
        }

        if ($token) {
            $request->attributes->set('user_session', $token);
            $token->touchRequest($request);

            // Store in session for next request
            if (!$session->has('user_session') || $session->get('user_session') !== $token->ulid) {
                $session->put('user_session', $token->ulid);
            }

            // Login user
            if (!Auth::check() || Auth::id() !== $token->user_id) {
                Auth::login($token->user);
            }
        }

        // If no valid token, ensure user is logged out
        if (!$token && Auth::check()) {
            return $this->fail($request);
        }

        // Lottery-based garbage collection
        $this->garbageCollection();

        return $next($request);
    }

    private function garbageCollection(): void
    {
        // Run lottery
        if (!$this->shouldRunGarbageCollection()) {
            return;
        }

        $now = now();

        // Delete expired remember sessions
        UserSession::whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->delete();

        // Delete inactive normal sessions
        UserSession::whereNull('expires_at')
            ->where('last_activity', '<', $now->copy()->subMinutes(config('session.lifetime')))
            ->delete();
    }

    private function shouldRunGarbageCollection(): bool
    {
        [$chances, $total] = config('session.lottery');

        return random_int(1, $total) <= $chances;
    }

    private function fail(Request $request): Response
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget('paymenter_remember'));
        Auth::logout();

        return redirect('/');
    }
}
