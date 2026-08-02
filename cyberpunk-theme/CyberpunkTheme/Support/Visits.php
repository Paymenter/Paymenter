<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

use Illuminate\Support\Facades\Cache;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Visit;

/**
 * Contador de visitas del sitio (empieza en cero cuando se instala el tema).
 */
class Visits
{
    /**
     * Registra una visita para el día de hoy.
     */
    public static function record(): void
    {
        try {
            $today = now()->toDateString();

            // increment() cita la columna según el motor de base de datos.
            $affected = Visit::where('day', $today)->increment('count');

            if ($affected === 0) {
                Visit::create(['day' => $today, 'count' => 1]);
            }

            Cache::forget('cyberpunk.visits');
        } catch (\Throwable $e) {
            // Nunca rompemos la petición por el contador.
        }
    }

    /**
     * Totales de visitas (cacheados 60 segundos).
     *
     * @return array{today:int,total:int}
     */
    public static function summary(): array
    {
        return Cache::remember('cyberpunk.visits', now()->addMinute(), function () {
            try {
                return [
                    'today' => (int) Visit::where('day', now()->toDateString())->value('count'),
                    'total' => (int) Visit::sum('count'),
                ];
            } catch (\Throwable $e) {
                return ['today' => 0, 'total' => 0];
            }
        });
    }

    /**
     * Reinicia el contador a cero.
     */
    public static function reset(): void
    {
        try {
            Visit::query()->delete();
        } catch (\Throwable $e) {
            // Ignorado
        }

        Cache::forget('cyberpunk.visits');
    }
}
