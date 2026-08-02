<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

/**
 * Paletas de colores predefinidas para cambiar el aspecto del tema con un clic.
 */
class Palettes
{
    public static function all(): array
    {
        return [
            'azul-rosa' => [
                'label' => 'Azul y Rosa (por defecto)',
                'preview' => ['#2563EB', '#F43FA5', '#38BDF8', '#0A1024'],
                'colors' => [
                    'primary' => 'hsl(217, 91%, 50%)',
                    'secondary' => 'hsl(330, 90%, 55%)',
                    'accent' => 'hsl(199, 92%, 48%)',
                    'neutral' => 'hsl(214, 32%, 85%)',
                    'base' => 'hsl(222, 44%, 12%)',
                    'muted' => 'hsl(215, 16%, 42%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(0, 0%, 100%)',
                    'background-secondary' => 'hsl(214, 45%, 97%)',
                    'dark-primary' => 'hsl(217, 91%, 60%)',
                    'dark-secondary' => 'hsl(330, 100%, 62%)',
                    'dark-accent' => 'hsl(199, 95%, 60%)',
                    'dark-neutral' => 'hsl(217, 33%, 24%)',
                    'dark-base' => 'hsl(0, 0%, 100%)',
                    'dark-muted' => 'hsl(215, 22%, 72%)',
                    'dark-inverted' => 'hsl(0, 0%, 100%)',
                    'dark-background' => 'hsl(222, 47%, 6%)',
                    'dark-background-secondary' => 'hsl(222, 40%, 10%)',
                ],
            ],
            'neon-rose' => [
                'label' => 'Neón Rosa',
                'preview' => ['#FF0080', '#FF1A33', '#D633FF', '#0A0A0A'],
                'colors' => [
                    'primary' => 'hsl(330, 100%, 50%)',
                    'secondary' => 'hsl(352, 96%, 52%)',
                    'accent' => 'hsl(288, 96%, 60%)',
                    'neutral' => 'hsl(330, 40%, 86%)',
                    'base' => 'hsl(330, 20%, 8%)',
                    'muted' => 'hsl(330, 10%, 45%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(330, 40%, 99%)',
                    'background-secondary' => 'hsl(330, 45%, 96%)',
                    'dark-primary' => 'hsl(330, 100%, 55%)',
                    'dark-secondary' => 'hsl(352, 100%, 55%)',
                    'dark-accent' => 'hsl(288, 100%, 65%)',
                    'dark-neutral' => 'hsl(330, 45%, 20%)',
                    'dark-base' => 'hsl(0, 0%, 100%)',
                    'dark-muted' => 'hsl(330, 20%, 68%)',
                    'dark-inverted' => 'hsl(0, 0%, 100%)',
                    'dark-background' => 'hsl(0, 0%, 4%)',
                    'dark-background-secondary' => 'hsl(330, 30%, 8%)',
                ],
            ],
            'blade-runner' => [
                'label' => 'Blade Runner (cian y magenta)',
                'preview' => ['#00E5FF', '#FF2D95', '#7B5CFF', '#050914'],
                'colors' => [
                    'primary' => 'hsl(187, 100%, 42%)',
                    'secondary' => 'hsl(325, 100%, 50%)',
                    'accent' => 'hsl(252, 100%, 62%)',
                    'neutral' => 'hsl(200, 30%, 85%)',
                    'base' => 'hsl(210, 30%, 10%)',
                    'muted' => 'hsl(205, 15%, 45%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(200, 40%, 99%)',
                    'background-secondary' => 'hsl(200, 40%, 96%)',
                    'dark-primary' => 'hsl(187, 100%, 50%)',
                    'dark-secondary' => 'hsl(325, 100%, 58%)',
                    'dark-accent' => 'hsl(252, 100%, 68%)',
                    'dark-neutral' => 'hsl(205, 40%, 20%)',
                    'dark-base' => 'hsl(0, 0%, 100%)',
                    'dark-muted' => 'hsl(200, 20%, 68%)',
                    'dark-inverted' => 'hsl(0, 0%, 100%)',
                    'dark-background' => 'hsl(222, 47%, 5%)',
                    'dark-background-secondary' => 'hsl(220, 40%, 8%)',
                ],
            ],
            'toxic-green' => [
                'label' => 'Matrix (verde tóxico)',
                'preview' => ['#00FF85', '#00C2FF', '#B4FF39', '#03120B'],
                'colors' => [
                    'primary' => 'hsl(151, 100%, 40%)',
                    'secondary' => 'hsl(195, 100%, 45%)',
                    'accent' => 'hsl(79, 100%, 45%)',
                    'neutral' => 'hsl(150, 25%, 85%)',
                    'base' => 'hsl(155, 30%, 8%)',
                    'muted' => 'hsl(150, 12%, 42%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(150, 30%, 99%)',
                    'background-secondary' => 'hsl(150, 30%, 96%)',
                    'dark-primary' => 'hsl(151, 100%, 50%)',
                    'dark-secondary' => 'hsl(195, 100%, 50%)',
                    'dark-accent' => 'hsl(79, 100%, 60%)',
                    'dark-neutral' => 'hsl(155, 35%, 18%)',
                    'dark-base' => 'hsl(0, 0%, 100%)',
                    'dark-muted' => 'hsl(150, 15%, 65%)',
                    'dark-inverted' => 'hsl(0, 0%, 100%)',
                    'dark-background' => 'hsl(155, 45%, 4%)',
                    'dark-background-secondary' => 'hsl(155, 35%, 7%)',
                ],
            ],
            'solar-flare' => [
                'label' => 'Solar (naranja y ámbar)',
                'preview' => ['#FF7A00', '#FFC300', '#FF3D00', '#120A02'],
                'colors' => [
                    'primary' => 'hsl(29, 100%, 50%)',
                    'secondary' => 'hsl(14, 100%, 50%)',
                    'accent' => 'hsl(46, 100%, 50%)',
                    'neutral' => 'hsl(30, 35%, 86%)',
                    'base' => 'hsl(25, 30%, 10%)',
                    'muted' => 'hsl(28, 15%, 45%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(35, 45%, 99%)',
                    'background-secondary' => 'hsl(35, 45%, 96%)',
                    'dark-primary' => 'hsl(29, 100%, 55%)',
                    'dark-secondary' => 'hsl(14, 100%, 55%)',
                    'dark-accent' => 'hsl(46, 100%, 55%)',
                    'dark-neutral' => 'hsl(28, 40%, 18%)',
                    'dark-base' => 'hsl(0, 0%, 100%)',
                    'dark-muted' => 'hsl(30, 20%, 68%)',
                    'dark-inverted' => 'hsl(0, 0%, 100%)',
                    'dark-background' => 'hsl(24, 40%, 4%)',
                    'dark-background-secondary' => 'hsl(24, 30%, 8%)',
                ],
            ],
            'vaporwave' => [
                'label' => 'Vaporwave (violeta y turquesa)',
                'preview' => ['#B14AED', '#3AF0E0', '#FF6AC1', '#0C0620'],
                'colors' => [
                    'primary' => 'hsl(280, 84%, 61%)',
                    'secondary' => 'hsl(322, 100%, 70%)',
                    'accent' => 'hsl(174, 86%, 58%)',
                    'neutral' => 'hsl(275, 30%, 86%)',
                    'base' => 'hsl(272, 35%, 10%)',
                    'muted' => 'hsl(275, 15%, 48%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(280, 45%, 99%)',
                    'background-secondary' => 'hsl(280, 45%, 96%)',
                    'dark-primary' => 'hsl(280, 90%, 66%)',
                    'dark-secondary' => 'hsl(322, 100%, 72%)',
                    'dark-accent' => 'hsl(174, 86%, 62%)',
                    'dark-neutral' => 'hsl(272, 40%, 22%)',
                    'dark-base' => 'hsl(0, 0%, 100%)',
                    'dark-muted' => 'hsl(275, 20%, 70%)',
                    'dark-inverted' => 'hsl(0, 0%, 100%)',
                    'dark-background' => 'hsl(258, 55%, 6%)',
                    'dark-background-secondary' => 'hsl(258, 45%, 10%)',
                ],
            ],
            'midnight-ice' => [
                'label' => 'Hielo (azul sobrio)',
                'preview' => ['#3B82F6', '#38BDF8', '#818CF8', '#0B1220'],
                'colors' => [
                    'primary' => 'hsl(217, 91%, 60%)',
                    'secondary' => 'hsl(199, 89%, 59%)',
                    'accent' => 'hsl(239, 84%, 67%)',
                    'neutral' => 'hsl(215, 25%, 86%)',
                    'base' => 'hsl(222, 35%, 12%)',
                    'muted' => 'hsl(215, 15%, 48%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(210, 40%, 99%)',
                    'background-secondary' => 'hsl(210, 40%, 96%)',
                    'dark-primary' => 'hsl(217, 91%, 65%)',
                    'dark-secondary' => 'hsl(199, 89%, 62%)',
                    'dark-accent' => 'hsl(239, 84%, 72%)',
                    'dark-neutral' => 'hsl(217, 33%, 22%)',
                    'dark-base' => 'hsl(0, 0%, 100%)',
                    'dark-muted' => 'hsl(215, 20%, 70%)',
                    'dark-inverted' => 'hsl(0, 0%, 100%)',
                    'dark-background' => 'hsl(222, 47%, 7%)',
                    'dark-background-secondary' => 'hsl(220, 40%, 11%)',
                ],
            ],
            'blood-moon' => [
                'label' => 'Luna de Sangre (rojo puro)',
                'preview' => ['#FF1F3D', '#FF5C39', '#FF2079', '#0A0203'],
                'colors' => [
                    'primary' => 'hsl(351, 100%, 50%)',
                    'secondary' => 'hsl(11, 100%, 55%)',
                    'accent' => 'hsl(336, 100%, 56%)',
                    'neutral' => 'hsl(350, 35%, 86%)',
                    'base' => 'hsl(350, 30%, 9%)',
                    'muted' => 'hsl(350, 12%, 45%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(0, 40%, 99%)',
                    'background-secondary' => 'hsl(0, 40%, 96%)',
                    'dark-primary' => 'hsl(351, 100%, 56%)',
                    'dark-secondary' => 'hsl(11, 100%, 60%)',
                    'dark-accent' => 'hsl(336, 100%, 62%)',
                    'dark-neutral' => 'hsl(350, 40%, 18%)',
                    'dark-base' => 'hsl(0, 0%, 100%)',
                    'dark-muted' => 'hsl(350, 15%, 66%)',
                    'dark-inverted' => 'hsl(0, 0%, 100%)',
                    'dark-background' => 'hsl(0, 45%, 3%)',
                    'dark-background-secondary' => 'hsl(352, 35%, 7%)',
                ],
            ],
            'mono-chrome' => [
                'label' => 'Monocromo (blanco y negro)',
                'preview' => ['#FFFFFF', '#A3A3A3', '#525252', '#000000'],
                'colors' => [
                    'primary' => 'hsl(0, 0%, 15%)',
                    'secondary' => 'hsl(0, 0%, 35%)',
                    'accent' => 'hsl(0, 0%, 55%)',
                    'neutral' => 'hsl(0, 0%, 85%)',
                    'base' => 'hsl(0, 0%, 5%)',
                    'muted' => 'hsl(0, 0%, 45%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(0, 0%, 100%)',
                    'background-secondary' => 'hsl(0, 0%, 96%)',
                    'dark-primary' => 'hsl(0, 0%, 96%)',
                    'dark-secondary' => 'hsl(0, 0%, 72%)',
                    'dark-accent' => 'hsl(0, 0%, 58%)',
                    'dark-neutral' => 'hsl(0, 0%, 20%)',
                    'dark-base' => 'hsl(0, 0%, 100%)',
                    'dark-muted' => 'hsl(0, 0%, 65%)',
                    'dark-inverted' => 'hsl(0, 0%, 5%)',
                    'dark-background' => 'hsl(0, 0%, 3%)',
                    'dark-background-secondary' => 'hsl(0, 0%, 8%)',
                ],
            ],
        ];
    }

    /**
     * Opciones para el <select> del panel.
     */
    public static function options(): array
    {
        return collect(self::all())->map(fn ($palette) => $palette['label'])->toArray();
    }

    /**
     * Colores de una paleta concreta.
     */
    public static function colors(string $key): array
    {
        return self::all()[$key]['colors'] ?? [];
    }
}
