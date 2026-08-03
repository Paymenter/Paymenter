<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

use App\Helpers\ExtensionHelper;
use App\Models\Setting;
use App\Providers\SettingsProvider;
use Illuminate\Support\Str;

/**
 * Lectura y escritura de la configuración del tema Cyberpunk.
 *
 * Los ajustes del tema se guardan en la tabla `settings` con el prefijo
 * `theme_cyberpunk_`, igual que hace Paymenter con cualquier tema, de forma
 * que el helper global theme() los encuentra sin cambios en el core.
 */
class Config
{
    public const THEME = 'cyberpunk';

    public const PREFIX = 'theme_cyberpunk_';

    /** Claves que se guardan como array (JSON) */
    public const ARRAY_KEYS = [
        'banner_slides',
        'marketing_words',
        'marketing_cards',
        'features',
        'custom_pages',
        'quick_links',
        'anim_light',
        'anim_dark',
    ];

    /** Claves booleanas */
    public const BOOL_KEYS = [
        'direct_checkout',
        'small_images',
        'show_category_description',
        'effect_neon',
        'effect_scanlines',
        'effect_grid',
        'effect_glitch',
        'effect_noise',
        'banner_enabled',
        'marketing_enabled',
        'marquee_enabled',
        'stats_enabled',
        'uptime_enabled',
        'visitors_enabled',
        'quick_links_enabled',
        'socials_enabled',
        'community_enabled',
        'reviews_enabled',
        'reviews_page_enabled',
        'avatar_uploads_enabled',
        'featured_reviews_enabled',
    ];

    /** Claves numéricas */
    public const INT_KEYS = [
        'banner_interval',
        'background_overlay',
        'community_media_limit',
        'featured_reviews_limit',
    ];

    /**
     * Lee un ajuste del tema.
     */
    public static function theme(string $key, mixed $default = null): mixed
    {
        $value = config('settings.' . self::PREFIX . $key, $default);

        return $value === null || $value === '' ? $default : $value;
    }

    /**
     * Lee un ajuste booleano del tema.
     */
    public static function themeBool(string $key, bool $default = false): bool
    {
        $value = config('settings.' . self::PREFIX . $key, null);

        if ($value === null || $value === '') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    /**
     * Lee un ajuste de tipo lista del tema.
     */
    public static function themeList(string $key, array $default = []): array
    {
        $value = config('settings.' . self::PREFIX . $key, null);

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        if (!is_array($value)) {
            return $default;
        }

        return array_values($value);
    }

    /**
     * Lee un ajuste propio de la extensión (no del tema).
     */
    public static function bool(string $key, bool $default = false): bool
    {
        try {
            $extension = ExtensionHelper::getExtension('other', 'CyberpunkTheme');
            $value = $extension?->config($key);
        } catch (\Throwable $e) {
            return $default;
        }

        if ($value === null || $value === '') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    /**
     * Guarda un conjunto de ajustes del tema.
     *
     * @param  array<string, mixed>  $values  claves SIN el prefijo
     */
    public static function save(array $values): void
    {
        foreach ($values as $key => $value) {
            $type = 'string';

            if (in_array($key, self::ARRAY_KEYS, true)) {
                $type = 'array';
                $value = json_encode(array_values(is_array($value) ? $value : []));
            } elseif (in_array($key, self::BOOL_KEYS, true)) {
                $type = 'boolean';
                $value = $value ? 1 : 0;
            } elseif (in_array($key, self::INT_KEYS, true)) {
                $type = 'integer';
                $value = (int) $value;
            }

            Setting::updateOrCreate(
                ['key' => self::PREFIX . $key, 'settingable_type' => null, 'settingable_id' => null],
                ['value' => $value, 'type' => $type, 'encrypted' => false]
            );
        }

        self::flush();
    }

    /**
     * Devuelve todos los ajustes del tema listos para rellenar el formulario.
     */
    public static function all(): array
    {
        $data = [];

        foreach (Defaults::settings() as $key => $default) {
            if (in_array($key, self::ARRAY_KEYS, true)) {
                $data[$key] = self::themeList($key, is_array($default) ? $default : []);
            } elseif (in_array($key, self::BOOL_KEYS, true)) {
                $data[$key] = self::themeBool($key, (bool) $default);
            } elseif (in_array($key, self::INT_KEYS, true)) {
                $data[$key] = (int) self::theme($key, $default);
            } else {
                $data[$key] = self::theme($key, $default);
            }
        }

        return $data;
    }

    /**
     * Restablece TODOS los ajustes del tema a los valores de fábrica.
     */
    public static function reset(): void
    {
        Setting::where('settingable_type', null)
            ->where('key', 'like', self::PREFIX . '%')
            ->delete();

        self::save(Defaults::settings());
    }

    /**
     * Slug de la sección de comunidad (limpio y seguro para una ruta).
     */
    public static function communitySlug(): string
    {
        $slug = (string) self::theme('community_slug', 'comunidad');
        $slug = Str::slug($slug);

        return $slug !== '' ? $slug : 'comunidad';
    }

    /**
     * Slug del apartado de reseñas.
     */
    public static function reviewsSlug(): string
    {
        $slug = Str::slug((string) self::theme('reviews_slug', 'resenas'));

        return $slug !== '' ? $slug : 'resenas';
    }

    /**
     * Limpia la caché de ajustes para que los cambios se apliquen ya.
     */
    public static function flush(): void
    {
        try {
            SettingsProvider::flushCache();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Cache::forget('settings');
        }

        \Illuminate\Support\Facades\Cache::forget('cyberpunk.home.stats');
        \Illuminate\Support\Facades\Cache::forget('cyberpunk.quick_links.auto');
        \Illuminate\Support\Facades\Cache::forget('cyberpunk.reviews.popular');
        \Illuminate\Support\Facades\Cache::forget('cyberpunk.reviews.stats');
        \Illuminate\Support\Facades\Cache::forget('cyberpunk.reviews.general');
        \Illuminate\Support\Facades\Cache::forget('cyberpunk.visits');
    }
}
