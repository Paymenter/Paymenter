<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Visits;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cuenta una visita por visitante y día (no cuenta bots evidentes, peticiones
 * de Livewire, ni el panel de administración).
 *
 * La marca se guarda en la sesión Y en una cookie: si el servidor se reinicia
 * y se pierden las sesiones, la cookie evita volver a contar al mismo
 * visitante el mismo día.
 */
class CountVisit
{
    /** Nombre de la marca con el último día contado. */
    public const COOKIE = 'cyberpunk_visit_day';

    public function handle(Request $request, Closure $next): Response
    {
        $contar = false;

        try {
            $contar = $this->shouldCount($request);

            if ($contar) {
                if ($request->hasSession()) {
                    $request->session()->put(self::COOKIE, now()->toDateString());
                }

                Visits::record();
            }
        } catch (\Throwable $e) {
            // El contador nunca debe romper el sitio.
            $contar = false;
        }

        $response = $next($request);

        if ($contar) {
            try {
                // Un año de duración, pero sólo guarda la fecha del último día
                // contado: al cambiar de día se vuelve a contar.
                $response->headers->setCookie(
                    Cookie::make(self::COOKIE, now()->toDateString(), 60 * 24 * 365)
                );
            } catch (\Throwable $e) {
                // Ignorado.
            }
        }

        return $response;
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

        $agent = strtolower((string) $request->userAgent());
        foreach (['bot', 'crawl', 'spider', 'slurp', 'monitor', 'uptime', 'curl', 'wget'] as $needle) {
            if ($agent !== '' && str_contains($agent, $needle)) {
                return false;
            }
        }

        $hoy = now()->toDateString();

        // ¿Ya se contó hoy? Miramos la sesión y, como respaldo, la cookie.
        if ($request->hasSession() && $request->session()->get(self::COOKIE) === $hoy) {
            return false;
        }

        if ($request->cookie(self::COOKIE) === $hoy) {
            return false;
        }

        return true;
    }
}
