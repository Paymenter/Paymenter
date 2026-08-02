<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\File;

/**
 * Instalador del tema.
 *
 * La extensión lleva el tema dentro (carpeta `theme/`) y los assets ya
 * compilados (carpeta `assets/`), de modo que al subir el ZIP desde el panel
 * de Paymenter el tema queda instalado y listo, sin necesidad de ejecutar
 * npm ni compilar nada en el servidor.
 */
class Installer
{
    /** Versión del paquete. Si cambia, se rehacen los valores de fábrica. */
    public const VERSION = '1.1.0';

    /**
     * Copia el tema, los assets compilados y crea los ajustes por defecto.
     */
    public static function install(bool $overwriteSettings = true, bool $activate = true): array
    {
        // Si el paquete instalado es más nuevo que lo guardado en la base de
        // datos, refrescamos los valores de fábrica (colores, marketing,
        // animaciones) para que una reinstalación traiga de verdad lo nuevo.
        if (!$overwriteSettings && self::storedVersion() !== self::VERSION) {
            $overwriteSettings = true;
        }

        $report = [
            'theme' => false,
            'assets' => false,
            'settings' => false,
            'activated' => false,
            'tables' => [],
            'errors' => [],
        ];

        // Lo primero: asegurar las tablas. Si una instalación anterior quedó
        // a medias, aquí se crean las que falten.
        try {
            $report['tables'] = Database::ensureTables();
        } catch (\Throwable $e) {
            $report['errors'][] = 'Base de datos: ' . $e->getMessage();
        }

        try {
            $report['theme'] = self::copyTheme();
        } catch (\Throwable $e) {
            $report['errors'][] = 'Tema: ' . $e->getMessage();
        }

        try {
            $report['assets'] = self::copyAssets();
        } catch (\Throwable $e) {
            $report['errors'][] = 'Assets: ' . $e->getMessage();
        }

        try {
            self::seedSettings($overwriteSettings);
            $report['settings'] = true;
        } catch (\Throwable $e) {
            $report['errors'][] = 'Ajustes: ' . $e->getMessage();
        }

        // Sólo activamos el tema si los estilos están en public/<tema>/.
        // Activarlo sin ellos dejaría la tienda sin diseño.
        if ($activate) {
            try {
                if (self::hasAssets()) {
                    $report['activated'] = self::activateTheme();
                } else {
                    $report['errors'][] = 'No se activó el tema porque faltan los assets compilados en public/' . Config::THEME . '. Copia la carpeta public/ del paquete o ejecuta: npm run build ' . Config::THEME;
                }
            } catch (\Throwable $e) {
                $report['errors'][] = 'Activación: ' . $e->getMessage();
            }
        }

        try {
            Setting::updateOrCreate(
                ['key' => Config::PREFIX . 'version', 'settingable_type' => null, 'settingable_id' => null],
                ['value' => self::VERSION, 'type' => 'string', 'encrypted' => false]
            );
        } catch (\Throwable $e) {
            // No es crítico.
        }

        Config::flush();

        return $report;
    }

    /**
     * Versión de los ajustes guardados en la base de datos.
     */
    public static function storedVersion(): ?string
    {
        try {
            return Setting::where('settingable_type', null)
                ->where('key', Config::PREFIX . 'version')
                ->value('value');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Copia themes/cyberpunk desde la extensión.
     */
    public static function copyTheme(): bool
    {
        $source = self::extensionPath('theme');
        $destination = base_path('themes/' . Config::THEME);

        if (!is_dir($source)) {
            // La extensión puede instalarse sin el tema dentro cuando éste ya
            // se copió a mano o con instalar.sh. No es un error.
            return false;
        }

        if (!is_dir(dirname($destination))) {
            File::makeDirectory(dirname($destination), 0755, true);
        }

        // Nunca borramos la carpeta destino entera para no perder
        // personalizaciones manuales; copiamos encima.
        return File::copyDirectory($source, $destination);
    }

    /**
     * Copia los assets ya compilados a public/cyberpunk.
     */
    public static function copyAssets(): bool
    {
        $source = self::extensionPath('assets');
        $destination = public_path(Config::THEME);

        if (!is_dir($source)) {
            // Sin assets precompilados el administrador puede ejecutar
            // `npm run build cyberpunk`; no es un error fatal.
            return false;
        }

        if (!is_dir($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        return File::copyDirectory($source, $destination);
    }

    /**
     * Crea los ajustes que aún no existan (o todos si $overwrite).
     */
    public static function seedSettings(bool $overwrite = false): void
    {
        Defaults::loadThemeHelpers();

        $defaults = Defaults::settings();

        if ($overwrite) {
            Config::save($defaults);

            return;
        }

        $existing = Setting::where('settingable_type', null)
            ->where('key', 'like', Config::PREFIX . '%')
            ->pluck('key')
            ->map(fn ($key) => substr($key, strlen(Config::PREFIX)))
            ->all();

        $missing = array_diff_key($defaults, array_flip($existing));

        if (count($missing) > 0) {
            Config::save($missing);
        }
    }

    /**
     * Marca el tema Cyberpunk como tema activo de la tienda.
     */
    public static function activateTheme(): bool
    {
        if (!is_dir(base_path('themes/' . Config::THEME))) {
            return false;
        }

        Setting::updateOrCreate(
            ['key' => 'theme', 'settingable_type' => null, 'settingable_id' => null],
            ['value' => Config::THEME, 'type' => 'string', 'encrypted' => false]
        );

        Config::flush();

        return true;
    }

    /**
     * ¿Está el tema Cyberpunk activo ahora mismo?
     */
    public static function isActive(): bool
    {
        return config('settings.theme', 'default') === Config::THEME;
    }

    /**
     * ¿Existen los assets compilados en public/?
     */
    public static function hasAssets(): bool
    {
        return file_exists(public_path(Config::THEME . '/manifest.json'))
            || file_exists(public_path(Config::THEME . '/.vite/manifest.json'));
    }

    private static function extensionPath(string $sub): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . $sub;
    }
}
