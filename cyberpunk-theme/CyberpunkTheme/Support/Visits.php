<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Visit;

/**
 * Contador de visitas del sitio.
 *
 * Se guarda por partida doble:
 *
 *  1. Una fila por día en ext_cyberpunk_visits (para el contador de "hoy").
 *  2. Un total acumulado en la tabla `settings`, que NUNCA baja.
 *
 * El total vive aparte a propósito: aunque se limpie la tabla de días, se
 * reinstale la extensión o se reinicie el servidor, las visitas totales
 * siguen ahí. El único que las pone a cero es el botón "Reiniciar visitas"
 * del panel.
 */
class Visits
{
    /** Clave del total acumulado dentro de los ajustes del tema. */
    public const TOTAL_KEY = 'visits_total';

    /**
     * Registra una visita para el día de hoy.
     */
    public static function record(): void
    {
        try {
            $today = now()->toDateString();

            // increment() cita la columna según el motor de base de datos.
            $affected = Visit::whereDate('day', $today)->increment('count');

            if ($affected === 0) {
                Visit::create(['day' => $today, 'count' => 1]);
            }
        } catch (\Throwable $e) {
            // Nunca rompemos la petición por el contador.
        }

        // El total acumulado se guarda aparte para que no dependa de la
        // tabla de días: aunque ésta se vacíe, el total se mantiene.
        try {
            self::bumpTotal();
        } catch (\Throwable $e) {
            // Ignorado.
        }

        Cache::forget('cyberpunk.visits');
    }

    /**
     * Suma uno al total acumulado guardado en los ajustes.
     */
    private static function bumpTotal(): void
    {
        $total = self::storedTotal() + 1;

        Setting::updateOrCreate(
            ['key' => Config::PREFIX . self::TOTAL_KEY, 'settingable_type' => null, 'settingable_id' => null],
            ['value' => $total, 'type' => 'integer', 'encrypted' => false]
        );

        // Sin flush() completo: sólo hay que refrescar este valor.
        Cache::forget('cyberpunk.visits.total');
    }

    /**
     * Total acumulado guardado (0 si nunca se ha contado nada).
     */
    public static function storedTotal(): int
    {
        return (int) Cache::remember('cyberpunk.visits.total', now()->addSeconds(30), function () {
            try {
                return (int) Setting::where('settingable_type', null)
                    ->where('key', Config::PREFIX . self::TOTAL_KEY)
                    ->value('value');
            } catch (\Throwable $e) {
                return 0;
            }
        });
    }

    /**
     * Totales de visitas (cacheados 60 segundos).
     *
     * @return array{today:int,total:int}
     */
    public static function summary(): array
    {
        return Cache::remember('cyberpunk.visits', now()->addMinute(), function () {
            $today = 0;
            $porDias = 0;

            try {
                $today = (int) Visit::whereDate('day', now()->toDateString())->value('count');
                $porDias = (int) Visit::sum('count');
            } catch (\Throwable $e) {
                // La tabla puede no existir todavía.
            }

            // El total nunca puede bajar: nos quedamos con el mayor de los dos.
            // Si el acumulado se quedó corto (instalaciones anteriores que sólo
            // tenían la tabla), lo ponemos al día.
            $acumulado = self::storedTotal();
            $total = max($acumulado, $porDias);

            if ($total > $acumulado) {
                try {
                    Setting::updateOrCreate(
                        ['key' => Config::PREFIX . self::TOTAL_KEY, 'settingable_type' => null, 'settingable_id' => null],
                        ['value' => $total, 'type' => 'integer', 'encrypted' => false]
                    );
                    Cache::forget('cyberpunk.visits.total');
                } catch (\Throwable $e) {
                    // Ignorado.
                }
            }

            return ['today' => $today, 'total' => $total];
        });
    }

    /**
     * Reinicia el contador a cero (tabla de días y total acumulado).
     */
    public static function reset(): void
    {
        try {
            Visit::query()->delete();
        } catch (\Throwable $e) {
            // Ignorado
        }

        try {
            Setting::updateOrCreate(
                ['key' => Config::PREFIX . self::TOTAL_KEY, 'settingable_type' => null, 'settingable_id' => null],
                ['value' => 0, 'type' => 'integer', 'encrypted' => false]
            );
        } catch (\Throwable $e) {
            // Ignorado
        }

        Cache::forget('cyberpunk.visits');
        Cache::forget('cyberpunk.visits.total');
    }
}
