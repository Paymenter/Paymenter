<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

/**
 * Categorías de las publicaciones de la comunidad.
 *
 * El usuario elige una al publicar (o ninguna, y entonces la publicación
 * sale como una publicación normal). Cada categoría tiene su color e icono
 * para que se distinga de un vistazo.
 */
class PostCategories
{
    public const GENERAL = 'general';

    public static function all(): array
    {
        return [
            'ayuda' => [
                'label' => 'Pido ayuda',
                'short' => 'Ayuda',
                'description' => '¿Algo no te funciona? Cuéntanos y la comunidad te echa una mano.',
                'icon' => 'ri-question-answer-fill',
                'color' => 'warning',
                'hex' => '#F59E0B',
            ],
            'solucion' => [
                'label' => 'Comparto una solución',
                'short' => 'Solución',
                'description' => 'Resolviste un problema y quieres que a otros les cueste menos.',
                'icon' => 'ri-lightbulb-flash-fill',
                'color' => 'success',
                'hex' => '#22C55E',
            ],
            'logro' => [
                'label' => 'Mi logro',
                'short' => 'Logro',
                'description' => 'Enseña lo que has montado con tu servidor o tu bot.',
                'icon' => 'ri-trophy-fill',
                'color' => 'accent',
                'hex' => '#38BDF8',
            ],
            'idea' => [
                'label' => 'Idea nueva',
                'short' => 'Idea',
                'description' => 'Propuestas y mejoras para el hosting o para la comunidad.',
                'icon' => 'ri-rocket-2-fill',
                'color' => 'secondary',
                'hex' => '#F43FA5',
            ],
            'tutorial' => [
                'label' => 'Tutorial / guía',
                'short' => 'Tutorial',
                'description' => 'Explica paso a paso cómo hacer algo.',
                'icon' => 'ri-book-open-fill',
                'color' => 'primary',
                'hex' => '#2563EB',
            ],
            'experiencia' => [
                'label' => 'Mi experiencia',
                'short' => 'Experiencia',
                'description' => 'Cuenta qué tal te va con el hosting: fotos, vídeos y opinión.',
                'icon' => 'ri-heart-3-fill',
                'color' => 'error',
                'hex' => '#EF4444',
            ],
            self::GENERAL => [
                'label' => 'Publicación normal',
                'short' => 'General',
                'description' => 'Cualquier otra cosa que quieras compartir.',
                'icon' => 'ri-chat-3-fill',
                'color' => 'muted',
                'hex' => '#94A3B8',
            ],
        ];
    }

    /**
     * Opciones para un <select> (clave => etiqueta).
     */
    public static function options(): array
    {
        return collect(self::all())->map(fn ($c) => $c['label'])->toArray();
    }

    public static function get(?string $key): array
    {
        $all = self::all();

        return $all[$key] ?? $all[self::GENERAL];
    }

    public static function exists(?string $key): bool
    {
        return $key !== null && array_key_exists($key, self::all());
    }

    /**
     * Normaliza el valor recibido del formulario.
     */
    public static function normalize(?string $key): string
    {
        return self::exists($key) ? $key : self::GENERAL;
    }
}
