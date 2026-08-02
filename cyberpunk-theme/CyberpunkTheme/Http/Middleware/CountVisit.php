<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Visits;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cuenta una visita por sesión y día (no cuenta bots evidentes, peticiones
 * de Livewire, ni el panel de administración).
 */
class CountVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if ($this->shouldCount($request)) {
                $request->session()->put('cyberpunk_visit_day', now()->toDateString());
                Visits::record();
            }
        } catch (\Throwable $e) {
            // El contador nunca debe romper el sitio.
        }

        return $next($request);
    }

    private function shouldCount(Request $request): bool
    {
        if (!$request->isMethod('GET') || $request->ajax() || $request->wantsJson()) {
            return false;
        }

        if ($request->hasHeader('X-Livewire')) {
            return false;
        }

        if ($request->is('admin', 'admin/*', 'livewire/*', 'api/*', 'storage/*')) {
            return false;
        }

        if (!$request->hasSession()) {
            return false;
        }

        $agent = strtolower((string) $request->userAgent());
        foreach (['bot', 'crawl', 'spider', 'slurp', 'monitor', 'uptime', 'curl', 'wget'] as $needle) {
            if ($agent !== '' && str_contains($agent, $needle)) {
                return false;
            }
        }

        return $request->session()->get('cyberpunk_visit_day') !== now()->toDateString();
    }
}
