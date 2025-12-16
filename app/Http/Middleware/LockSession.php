<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LockSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $setting = config('settings.session_validation', 'none');
        if (!auth()->check() || $setting === 'none') {
            return $next($request);
        }

        $isAdmin = auth()->user()->role_id !== null;

        $ipModes = [
            'admin' => ['ip_admin', 'ip_both', 'ip_user_agent_admin', 'ip_user_agent_both'],
            'client' => ['ip_client', 'ip_both', 'ip_user_agent_client', 'ip_user_agent_both'],
        ];

        $uaModes = [
            'admin' => ['user_agent_admin', 'user_agent', 'ip_user_agent_admin', 'ip_user_agent_both'],
            'client' => ['user_agent_client', 'user_agent', 'ip_user_agent_client', 'ip_user_agent_both'],
        ];

        $role = $isAdmin ? 'admin' : 'client';

        if (in_array($setting, $ipModes[$role], true)) {
            if ($resp = $this->checkAndStore($request->session(), 'login_ip', $request->ip(), $request)) {
                return $resp;
            }
        }

        if (in_array($setting, $uaModes[$role], true)) {
            if ($resp = $this->checkAndStore($request->session(), 'login_ua', hash('sha256', $request->header('User-Agent')), $request)) {
                return $resp;
            }
        }

        return $next($request);
    }

    private function checkAndStore(Session $session, string $key, string $value, $request)
    {
        $stored = $session->get($key);

        if ($stored === null) {
            $session->put($key, $value);
        } elseif ($stored !== $value) {
            return $this->invalidateSession($request);
        }

        return null;
    }

    private function invalidateSession(Request $request)
    {
        app(\App\Actions\Auth\Logout::class)->execute();

        return redirect('/');
    }
}
