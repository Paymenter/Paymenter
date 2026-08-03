<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

/**
 * Utilidades de texto para el contenido que escriben los usuarios.
 */
class Text
{
    /**
     * Escapa el texto y convierte las direcciones web en enlaces pinchables.
     *
     * La implementación vive en el tema (cyber_linkify) para que sea la misma
     * en todas partes; aquí sólo hay una copia de emergencia por si el tema
     * Cyberpunk no está activo.
     */
    public static function linkify(?string $text): string
    {
        Defaults::loadThemeHelpers();

        if (function_exists('cyber_linkify')) {
            return cyber_linkify($text);
        }

        $escaped = e((string) $text);

        $html = preg_replace_callback(
            '~(?<![\w@/])((?:https?://|www\.)[^\s<]+)~i',
            function (array $match): string {
                $url = rtrim($match[1], '.,;:!?)]}\'"');
                $trail = substr($match[1], strlen($url));
                $href = preg_match('~^https?://~i', $url) ? $url : 'https://' . $url;

                return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer nofollow" class="cyber-link">'
                    . $url . '</a>' . $trail;
            },
            $escaped
        );

        return $html ?? $escaped;
    }
}
