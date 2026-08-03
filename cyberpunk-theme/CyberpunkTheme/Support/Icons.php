<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

use BladeUI\Icons\Factory as IconFactory;
use Illuminate\Support\Str;

/**
 * Catálogo de iconos para el selector visual del panel.
 *
 * Los iconos se leen del propio paquete de iconos que ya usa Paymenter
 * (Remix Icon), así que siempre existen y no hay que mantener una lista a mano.
 */
class Icons
{
    /** Iconos que se muestran de entrada, sin buscar nada. */
    public const FEATURED = [
        'ri-server-fill',
        'ri-cloud-fill',
        'ri-database-2-fill',
        'ri-cpu-line',
        'ri-hard-drive-2-fill',
        'ri-global-line',
        'ri-earth-fill',
        'ri-code-s-slash-fill',
        'ri-terminal-box-fill',
        'ri-braces-fill',
        'ri-robot-2-fill',
        'ri-whatsapp-fill',
        'ri-discord-fill',
        'ri-telegram-fill',
        'ri-instagram-fill',
        'ri-facebook-circle-fill',
        'ri-youtube-fill',
        'ri-tiktok-fill',
        'ri-twitter-x-fill',
        'ri-github-fill',
        'ri-rocket-2-fill',
        'ri-flashlight-fill',
        'ri-shield-check-fill',
        'ri-shield-flash-fill',
        'ri-lock-2-fill',
        'ri-customer-service-2-fill',
        'ri-chat-smile-2-fill',
        'ri-headphone-fill',
        'ri-price-tag-3-fill',
        'ri-money-dollar-circle-fill',
        'ri-shopping-bag-3-fill',
        'ri-store-2-fill',
        'ri-gamepad-fill',
        'ri-game-fill',
        'ri-timer-flash-fill',
        'ri-speed-up-fill',
        'ri-line-chart-fill',
        'ri-bar-chart-box-fill',
        'ri-team-fill',
        'ri-user-star-fill',
        'ri-star-smile-fill',
        'ri-heart-3-fill',
        'ri-fire-fill',
        'ri-trophy-fill',
        'ri-lightbulb-flash-fill',
        'ri-magic-fill',
        'ri-settings-4-fill',
        'ri-tools-fill',
        'ri-book-open-fill',
        'ri-file-text-fill',
        'ri-mail-send-fill',
        'ri-notification-3-fill',
        'ri-check-double-fill',
        'ri-infinity-fill',
        'ri-24-hours-fill',
        'ri-calendar-check-fill',
        'ri-map-pin-2-fill',
        'ri-window-fill',
        'ri-smartphone-fill',
        'ri-links-fill',
    ];

    /**
     * Todos los iconos disponibles: 'ri-xxx' => 'xxx'.
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        $icons = [];

        foreach (self::sets() as $prefix => $paths) {
            foreach ($paths as $path) {
                foreach (glob(rtrim($path, '/') . '/*.svg') ?: [] as $file) {
                    $name = basename($file, '.svg');
                    $icons[$prefix . '-' . $name] = str_replace('-', ' ', $name);
                }
            }
        }

        ksort($icons);

        return $cache = $icons;
    }

    /**
     * Opciones para un Select de Filament, con el icono dibujado al lado.
     *
     * @return array<string, string>
     */
    public static function options(?string $search = null, int $limit = 60): array
    {
        $search = trim(Str::lower((string) $search));

        if ($search === '') {
            $names = array_values(array_filter(self::FEATURED, fn ($icon) => isset(self::all()[$icon])));
        } else {
            $needle = str_replace([' ', '_'], '-', $search);
            $needle = Str::startsWith($needle, 'ri-') ? substr($needle, 3) : $needle;

            $names = [];
            foreach (self::all() as $icon => $label) {
                if (str_contains($icon, $needle)) {
                    $names[] = $icon;

                    if (count($names) >= $limit) {
                        break;
                    }
                }
            }
        }

        $options = [];

        foreach ($names as $icon) {
            $options[$icon] = self::label($icon);
        }

        return $options;
    }

    /**
     * Etiqueta HTML de un icono (dibujo + nombre) para el selector.
     */
    public static function label(?string $icon): string
    {
        $icon = trim((string) $icon);

        if ($icon === '') {
            return '';
        }

        $name = e(str_replace('-', ' ', preg_replace('/^ri-/', '', $icon)));

        return '<span class="flex items-center gap-2">'
            . self::svg($icon)
            . '<span>' . $name . '</span>'
            . '</span>';
    }

    /**
     * SVG en línea de un icono, listo para incrustar.
     */
    public static function svg(string $icon, string $class = 'w-5 h-5 shrink-0'): string
    {
        foreach (self::sets() as $prefix => $paths) {
            if (!Str::startsWith($icon, $prefix . '-')) {
                continue;
            }

            $name = substr($icon, strlen($prefix) + 1);

            foreach ($paths as $path) {
                $file = rtrim($path, '/') . '/' . $name . '.svg';

                if (is_file($file)) {
                    $svg = (string) file_get_contents($file);

                    return (string) preg_replace(
                        '/<svg /',
                        '<svg class="' . e($class) . '" ',
                        $svg,
                        1
                    );
                }
            }
        }

        return '<span class="w-5 h-5 shrink-0"></span>';
    }

    /**
     * ¿Existe este icono?
     */
    public static function exists(?string $icon): bool
    {
        return $icon !== null && $icon !== '' && isset(self::all()[$icon]);
    }

    /**
     * Conjuntos de iconos registrados en la aplicación: prefijo => rutas.
     *
     * @return array<string, array<int, string>>
     */
    protected static function sets(): array
    {
        static $sets = null;

        if ($sets !== null) {
            return $sets;
        }

        $sets = [];

        try {
            foreach (app(IconFactory::class)->all() as $set) {
                $prefix = $set['prefix'] ?? null;
                $paths = $set['paths'] ?? ($set['path'] ?? null);

                if (!$prefix || !$paths) {
                    continue;
                }

                $sets[$prefix] = array_values(array_filter((array) $paths, 'is_dir'));
            }
        } catch (\Throwable $e) {
            $sets = [];
        }

        // Respaldo: si por lo que sea no pudimos leer los sets, usamos el
        // paquete de Remix Icon que Paymenter trae de serie.
        if (count($sets) === 0) {
            $fallback = base_path('vendor/andreiio/blade-remix-icon/resources/svg');

            if (is_dir($fallback)) {
                $sets = ['ri' => [$fallback]];
            }
        }

        return $sets;
    }
}
