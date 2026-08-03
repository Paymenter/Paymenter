<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

/**
 * Valores de fábrica del tema Cyberpunk.
 *
 * El contenido de marketing por defecto vive en themes/cyberpunk/theme.php
 * (función cyber_defaults) para que el tema funcione aunque la extensión no
 * esté activa. Aquí sólo lo cargamos para no duplicarlo.
 */
class Defaults
{
    /**
     * Contenido de marketing por defecto.
     */
    public static function marketing(string $key): array
    {
        self::loadThemeHelpers();

        if (function_exists('cyber_defaults')) {
            return cyber_defaults($key);
        }

        return [];
    }

    /**
     * Carga themes/cyberpunk/theme.php si sus helpers aún no están definidos
     * (ocurre cuando el tema todavía no está activo).
     */
    public static function loadThemeHelpers(): void
    {
        if (function_exists('cyber_defaults')) {
            return;
        }

        $path = base_path('themes/' . Config::THEME . '/theme.php');

        if (file_exists($path)) {
            require_once $path;
        }
    }

    /**
     * Mapa completo clave => valor por defecto.
     */
    public static function settings(): array
    {
        return [
            // General
            'direct_checkout' => false,
            'small_images' => false,
            'image_mode' => 'full',
            'show_category_description' => true,
            'logo_display' => 'logo-and-name',
            'home_page_text' => '',
            'footer_text' => 'Infraestructura de alto rendimiento para tus webs, bots y aplicaciones.',

            // Efectos
            'effect_neon' => true,
            'effect_scanlines' => true,
            'effect_grid' => true,
            'effect_glitch' => true,
            'effect_noise' => false,
            'font_family' => 'system',
            'background_image' => '',
            'background_overlay' => 80,

            // Secciones
            'banner_enabled' => true,
            'banner_interval' => 6000,
            'marketing_enabled' => true,
            'marquee_enabled' => false,
            'marketing_title' => '¿Qué puedes montar con nosotros?',
            'marketing_subtitle' => 'Infraestructura preparada para tus webs, bots y aplicaciones. Elige tu plan y despliega en minutos.',
            'stats_enabled' => true,
            'uptime_enabled' => true,
            // Se rellena sola al instalar y no se toca nunca más, para que la
            // cuenta del tiempo activo no se reinicie.
            'uptime_start' => '',
            'visitors_enabled' => true,
            'quick_links_enabled' => true,
            'socials_enabled' => true,

            // Comunidad y reseñas
            'community_enabled' => true,
            'community_name' => 'Comunidad',
            'community_slug' => 'comunidad',
            'community_description' => 'Pide ayuda, comparte soluciones, enseña tus logros y propón ideas nuevas. Aquí nos ayudamos entre todos.',
            'community_media_limit' => 4,
            'reviews_enabled' => true,
            'avatar_uploads_enabled' => true,

            // Apartado público de reseñas
            'reviews_page_enabled' => true,
            'reviews_name' => 'Reseñas',
            'reviews_slug' => 'resenas',
            'reviews_description' => 'Puntúa con estrellas y cuenta tu experiencia. Para dejar una valoración hay que escribir una reseña.',

            // Reseñas del servicio en general (no de un plan concreto)
            'general_reviews_name' => 'El servicio en general',
            'general_reviews_description' => 'Qué opinan los clientes del servicio en conjunto: soporte, velocidad, estabilidad y trato.',

            // Reseñas destacadas en el inicio
            'featured_reviews_enabled' => true,
            'featured_reviews_title' => 'Lo que dicen nuestros clientes',
            'featured_reviews_subtitle' => 'Opiniones reales de gente que ya tiene sus servicios con nosotros.',
            'featured_reviews_limit' => 3,
            'featured_reviews_mode' => 'mixed',

            // Posición del marketing
            'marketing_align' => 'left',

            // Redes sociales
            'social_facebook' => '',
            'social_discord' => '',
            'social_instagram' => '',
            'social_whatsapp_channel' => '',
            'social_whatsapp_group' => '',
            'social_telegram' => '',
            'social_youtube' => '',
            'social_tiktok' => '',
            'social_x' => '',
            'social_github' => '',

            // Colores (claro) — paleta por defecto: azul + rosa sobre blanco
            'primary' => 'hsl(217, 91%, 50%)',
            'secondary' => 'hsl(330, 90%, 55%)',
            'accent' => 'hsl(199, 92%, 48%)',
            'neutral' => 'hsl(214, 32%, 85%)',
            'base' => 'hsl(222, 44%, 12%)',
            'muted' => 'hsl(215, 16%, 42%)',
            'inverted' => 'hsl(0, 0%, 100%)',
            'background' => 'hsl(0, 0%, 100%)',
            'background-secondary' => 'hsl(214, 45%, 97%)',

            // Colores (oscuro) — azul + rosa sobre azul noche
            'dark-primary' => 'hsl(217, 91%, 60%)',
            'dark-secondary' => 'hsl(330, 100%, 62%)',
            'dark-accent' => 'hsl(199, 95%, 60%)',
            'dark-neutral' => 'hsl(217, 33%, 24%)',
            'dark-base' => 'hsl(0, 0%, 100%)',
            'dark-muted' => 'hsl(215, 22%, 72%)',
            'dark-inverted' => 'hsl(0, 0%, 100%)',
            'dark-background' => 'hsl(222, 47%, 6%)',
            'dark-background-secondary' => 'hsl(222, 40%, 10%)',

            // Listas
            'banner_slides' => self::marketing('banner_slides'),
            'marketing_words' => self::marketing('marketing_words'),
            'marketing_cards' => self::marketing('marketing_cards'),
            'features' => self::marketing('features'),
            'custom_pages' => [],
            'quick_links' => [],

            // Animaciones de fondo (se pueden combinar varias por modo)
            'anim_light' => ['clouds'],
            'anim_dark' => ['stars', 'shooting'],
        ];
    }
}
