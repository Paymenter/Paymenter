<?php

/*
|--------------------------------------------------------------------------
| Cyberpunk Theme for Paymenter
|--------------------------------------------------------------------------
|
| Author : Sky Ultra Plus
| Colors : Rosa / Rojo / Negro (neón) por defecto
|
| Este archivo es cargado en cada request por App\Classes\Theme, por lo que
| aprovechamos para declarar los helpers del tema. Todos los helpers están
| protegidos con function_exists() para que el archivo pueda incluirse más
| de una vez sin provocar errores de redeclaración.
|
*/

if (!function_exists('cyber_cfg')) {
    /**
     * Lee un ajuste del tema (theme_cyberpunk_<key>).
     */
    function cyber_cfg(string $key, mixed $default = null): mixed
    {
        $value = theme($key, $default);

        return $value === null || $value === '' ? $default : $value;
    }
}

if (!function_exists('cyber_bool')) {
    /**
     * Lee un ajuste booleano del tema (soporta "0"/"1" guardados como texto).
     */
    function cyber_bool(string $key, bool $default = false): bool
    {
        $value = theme($key, null);

        if ($value === null || $value === '') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }
}

if (!function_exists('cyber_list')) {
    /**
     * Lee un ajuste que guarda una lista (array) del tema.
     * Acepta tanto arrays ya casteados como cadenas JSON.
     */
    function cyber_list(string $key, array $default = []): array
    {
        $value = theme($key, null);

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        if (!is_array($value) || count($value) === 0) {
            return $default;
        }

        return array_values($value);
    }
}

if (!function_exists('cyber_ext')) {
    /**
     * ¿Está la extensión CyberpunkTheme instalada Y activa?
     *
     * No basta con comprobar que la clase existe (los archivos pueden estar
     * ahí con la extensión desactivada): la extensión marca esta bandera en
     * su boot(), que es cuando registra rutas y componentes Livewire.
     */
    function cyber_ext(): bool
    {
        return (bool) config('cyberpunk.booted', false);
    }
}

if (!function_exists('cyber_media')) {
    /**
     * Convierte un valor guardado por el panel (ruta en disco público o URL)
     * en una URL utilizable dentro del tema.
     */
    function cyber_media(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//') || str_starts_with($path, 'data:')) {
            return $path;
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    }
}

if (!function_exists('cyber_avatar')) {
    /**
     * Avatar del usuario respetando la subida personalizada del tema.
     */
    function cyber_avatar($user): string
    {
        if (!$user) {
            return 'https://www.gravatar.com/avatar/?d=mp';
        }

        if (cyber_ext()) {
            $custom = \Paymenter\Extensions\Others\CyberpunkTheme\Support\Avatars::url($user);
            if ($custom) {
                return $custom;
            }
        }

        return $user->avatar ?? 'https://www.gravatar.com/avatar/?d=mp';
    }
}

if (!function_exists('cyber_socials')) {
    /**
     * Redes sociales configuradas. Devuelve sólo las que tienen URL.
     */
    function cyber_socials(): array
    {
        $networks = [
            'facebook' => ['label' => 'Facebook', 'icon' => 'ri-facebook-circle-fill', 'color' => '#1877F2'],
            'discord' => ['label' => 'Discord', 'icon' => 'ri-discord-fill', 'color' => '#5865F2'],
            'instagram' => ['label' => 'Instagram', 'icon' => 'ri-instagram-fill', 'color' => '#E1306C'],
            'whatsapp_channel' => ['label' => 'Canal de WhatsApp', 'icon' => 'ri-whatsapp-fill', 'color' => '#25D366'],
            'whatsapp_group' => ['label' => 'Grupo de WhatsApp', 'icon' => 'ri-group-2-fill', 'color' => '#25D366'],
            'telegram' => ['label' => 'Telegram', 'icon' => 'ri-telegram-fill', 'color' => '#229ED9'],
            'youtube' => ['label' => 'YouTube', 'icon' => 'ri-youtube-fill', 'color' => '#FF0000'],
            'tiktok' => ['label' => 'TikTok', 'icon' => 'ri-tiktok-fill', 'color' => '#FE2C55'],
            'x' => ['label' => 'X / Twitter', 'icon' => 'ri-twitter-x-fill', 'color' => '#FFFFFF'],
            'github' => ['label' => 'GitHub', 'icon' => 'ri-github-fill', 'color' => '#FFFFFF'],
        ];

        $links = [];
        foreach ($networks as $key => $meta) {
            $url = cyber_cfg('social_' . $key);
            if ($url) {
                $links[] = $meta + ['key' => $key, 'url' => $url];
            }
        }

        return $links;
    }
}

if (!function_exists('cyber_animations')) {
    /**
     * Catálogo de animaciones de fondo disponibles.
     *
     * @return array<string, string>
     */
    function cyber_animations(): array
    {
        return [
            'stars' => 'Estrellas (cielo estrellado)',
            'shooting' => 'Estrellas fugaces',
            'planets' => 'Planetas flotando',
            'clouds' => 'Nubes en movimiento',
            'rain' => 'Lluvia',
            'storm' => 'Truenos y relámpagos',
            'snow' => 'Nieve',
            'matrix' => 'Lluvia digital (matrix)',
            'aurora' => 'Aurora de colores',
        ];
    }
}

if (!function_exists('cyber_anims')) {
    /**
     * Animaciones activas para un modo ('light' u 'dark').
     * Se pueden combinar varias entre sí.
     *
     * @return array<int, string>
     */
    function cyber_anims(string $mode): array
    {
        $default = $mode === 'dark' ? ['stars', 'shooting'] : ['clouds'];

        $value = cyber_list('anim_' . $mode, $default);

        $valid = array_keys(cyber_animations());

        return array_values(array_filter($value, fn ($a) => is_string($a) && in_array($a, $valid, true)));
    }
}

if (!function_exists('cyber_words')) {
    /**
     * Frases de marketing en movimiento, como lista plana de textos.
     * Acepta tanto ["frase"] como [['text' => 'frase']].
     */
    function cyber_words(): array
    {
        $items = cyber_list('marketing_words', cyber_defaults('marketing_words'));

        $words = [];
        foreach ($items as $item) {
            if (is_string($item)) {
                $text = trim($item);
            } elseif (is_array($item)) {
                $text = trim((string) ($item['text'] ?? ''));
            } else {
                continue;
            }

            if ($text !== '') {
                $words[] = $text;
            }
        }

        return $words;
    }
}

if (!function_exists('cyber_defaults')) {
    /**
     * Contenido de marketing por defecto (Sky Ultra Plus, en español).
     * El administrador puede sobreescribir todo desde el panel.
     */
    function cyber_defaults(string $key): array
    {
        $defaults = [
            'banner_slides' => [
                [
                    'title' => 'Sky Ultra Plus',
                    'subtitle' => 'Hosting de alto rendimiento para tus webs, bots y aplicaciones. Activación instantánea, discos NVMe y soporte 24/7 en español.',
                    'button_label' => 'Ver planes',
                    'button_url' => '',
                    'image' => '',
                    'enabled' => true,
                ],
                [
                    'title' => 'Tu web online en minutos',
                    'subtitle' => 'Alojamiento web con SSL gratis, panel sencillo y copias de seguridad. Ideal para tu negocio, tu tienda o tu portafolio.',
                    'button_label' => 'Quiero mi web',
                    'button_url' => '',
                    'image' => '',
                    'enabled' => true,
                ],
                [
                    'title' => 'Bots que nunca se duermen',
                    'subtitle' => 'Servidores para bots de WhatsApp, Discord y Telegram con reinicio automático, consola en vivo y sesión estable 24/7.',
                    'button_label' => 'Alojar mi bot',
                    'button_url' => '',
                    'image' => '',
                    'enabled' => true,
                ],
                [
                    'title' => 'PreBots listos para usar',
                    'subtitle' => 'Bots de WhatsApp, Discord y Telegram ya configurados. Los instalamos por ti: tú solo los enciendes y empiezas.',
                    'button_label' => 'Ver PreBots',
                    'button_url' => '',
                    'image' => '',
                    'enabled' => true,
                ],
                [
                    'title' => 'Python y JavaScript sin límites',
                    'subtitle' => 'Despliega tus scripts, APIs y aplicaciones Node.js con recursos dedicados, dominio propio y protección Anti-DDoS incluida.',
                    'button_label' => 'Empezar ahora',
                    'button_url' => '',
                    'image' => '',
                    'enabled' => true,
                ],
            ],
            'marketing_words' => [
                'Servidores para páginas web',
                'Servidores Python 24/7',
                'Servidores JavaScript y Node.js',
                'Bots de WhatsApp siempre online',
                'Bots de Discord sin caídas',
                'Bots de Telegram al instante',
                'PreBots listos para instalar',
                'Activación instantánea',
                'Discos NVMe ultra rápidos',
                'Protección Anti-DDoS incluida',
                'Soporte 24/7 en español',
                'Precios pensados para crecer',
            ],
            'marketing_cards' => [
                [
                    'title' => 'Hosting web',
                    'description' => 'Monta tu página o tienda online con SSL gratis, dominio propio y un panel fácil de usar. Copias de seguridad automáticas.',
                    'icon' => 'ri-global-fill',
                    'url' => '',
                    'enabled' => true,
                ],
                [
                    'title' => 'Servidores Python',
                    'description' => 'Ejecuta tus scripts, APIs y bots en Python las 24 horas, con recursos dedicados y consola en tiempo real.',
                    'icon' => 'ri-code-box-fill',
                    'url' => '',
                    'enabled' => true,
                ],
                [
                    'title' => 'Servidores JavaScript',
                    'description' => 'Node.js listo para tus apps, APIs y proyectos en tiempo real. Despliega desde Git y escala cuando lo necesites.',
                    'icon' => 'ri-javascript-fill',
                    'url' => '',
                    'enabled' => true,
                ],
                [
                    'title' => 'Bots de WhatsApp',
                    'description' => 'Aloja tu bot de WhatsApp con sesión estable, reinicios automáticos y almacenamiento persistente.',
                    'icon' => 'ri-whatsapp-fill',
                    'url' => '',
                    'enabled' => true,
                ],
                [
                    'title' => 'Bots de Discord',
                    'description' => 'Tu bot de Discord siempre online, con logs, consola en vivo y actualizaciones sin tiempo de inactividad.',
                    'icon' => 'ri-discord-fill',
                    'url' => '',
                    'enabled' => true,
                ],
                [
                    'title' => 'Bots de Telegram',
                    'description' => 'Despliega bots de Telegram en segundos, con webhooks listos y recursos que crecen contigo.',
                    'icon' => 'ri-telegram-fill',
                    'url' => '',
                    'enabled' => true,
                ],
                [
                    'title' => 'PreBots instalados',
                    'description' => 'Bots ya configurados de WhatsApp, Discord y Telegram. Nosotros los instalamos, tú solo los usas.',
                    'icon' => 'ri-robot-2-fill',
                    'url' => '',
                    'enabled' => true,
                ],
                [
                    'title' => 'Soporte 24/7',
                    'description' => 'Equipo en español disponible todos los días por tickets y WhatsApp. Te ayudamos a montarlo todo.',
                    'icon' => 'ri-customer-service-2-fill',
                    'url' => '',
                    'enabled' => true,
                ],
            ],
            'features' => [
                ['title' => 'Activación instantánea', 'description' => 'Tu servicio se crea solo en cuanto se confirma el pago.', 'icon' => 'ri-flashlight-fill'],
                ['title' => 'Discos NVMe', 'description' => 'Almacenamiento ultra rápido para cargas exigentes.', 'icon' => 'ri-hard-drive-3-fill'],
                ['title' => 'Protección Anti-DDoS', 'description' => 'Mitigación activa incluida en todos los planes.', 'icon' => 'ri-shield-flash-fill'],
                ['title' => 'Panel completo', 'description' => 'Consola, archivos, backups y estadísticas en vivo.', 'icon' => 'ri-dashboard-3-fill'],
                ['title' => 'Soporte en español', 'description' => 'Atención real por ticket y WhatsApp todos los días.', 'icon' => 'ri-customer-service-2-fill'],
            ],
        ];

        return $defaults[$key] ?? [];
    }
}

if (!function_exists('cyber_marketing')) {
    /**
     * Devuelve un bloque de marketing configurado o el valor por defecto.
     * Sólo devuelve los elementos habilitados.
     */
    function cyber_marketing(string $key): array
    {
        $items = cyber_list($key, cyber_defaults($key));

        return array_values(array_filter($items, function ($item) {
            if (!isset($item['enabled'])) {
                return true;
            }

            return filter_var($item['enabled'], FILTER_VALIDATE_BOOLEAN);
        }));
    }
}

if (!function_exists('cyber_popular_category')) {
    /**
     * Categoría con más "me gusta" + comentarios entre sus productos.
     * Devuelve el id de la categoría o null.
     */
    function cyber_popular_category(): ?int
    {
        if (!cyber_ext() || !cyber_bool('reviews_enabled', true)) {
            return null;
        }

        return \Illuminate\Support\Facades\Cache::remember('cyberpunk.popular_category', now()->addMinutes(5), function () {
            try {
                $stats = \Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews::allStats();

                if (count($stats) === 0) {
                    return null;
                }

                $scores = [];
                $products = \App\Models\Product::whereIn('id', array_keys($stats))
                    ->pluck('category_id', 'id');

                foreach ($stats as $productId => $row) {
                    $categoryId = $products[$productId] ?? null;
                    if (!$categoryId) {
                        continue;
                    }
                    $scores[$categoryId] = ($scores[$categoryId] ?? 0) + ($row['likes'] * 2) + $row['comments'];
                }

                if (count($scores) === 0) {
                    return null;
                }

                arsort($scores);

                return (int) array_key_first($scores);
            } catch (\Throwable $e) {
                return null;
            }
        });
    }
}

if (!function_exists('cyber_align')) {
    /**
     * Clases de alineación del marketing (izquierda, centro o derecha).
     *
     * @return array{wrapper:string,text:string,items:string}
     */
    function cyber_align(): array
    {
        return match (cyber_cfg('marketing_align', 'left')) {
            'center' => ['wrapper' => 'mx-auto', 'text' => 'text-center', 'items' => 'items-center justify-center'],
            'right' => ['wrapper' => 'ml-auto', 'text' => 'text-right', 'items' => 'items-end justify-end'],
            default => ['wrapper' => '', 'text' => 'text-left', 'items' => 'items-start justify-start'],
        };
    }
}

if (!function_exists('cyber_stats')) {
    /**
     * Estadísticas mostradas en el inicio. Se cachean 5 minutos.
     */
    function cyber_stats(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('cyberpunk.home.stats', now()->addMinutes(5), function () {
            $stats = [
                'clients' => 0,
                'services' => 0,
                'products' => 0,
                'orders' => 0,
                'uptime_start' => null,
                'visits_today' => 0,
                'visits_total' => 0,
            ];

            try {
                $stats['clients'] = \App\Models\User::count();
                $stats['services'] = \App\Models\Service::where('status', 'active')->count();
                $stats['products'] = \App\Models\Product::where('hidden', false)->count();
                $stats['orders'] = \App\Models\Invoice::count();

                $oldest = \App\Models\Invoice::orderBy('created_at')->value('created_at')
                    ?? \App\Models\Order::orderBy('created_at')->value('created_at')
                    ?? \App\Models\User::orderBy('created_at')->value('created_at');

                $stats['uptime_start'] = $oldest ? \Illuminate\Support\Carbon::parse($oldest)->timestamp : null;
            } catch (\Throwable $e) {
                // Base de datos no disponible todavía (instalación): devolvemos ceros.
            }

            return $stats;
        });
    }
}

if (!function_exists('cyber_visits')) {
    /**
     * Contador de visitas (necesita la extensión CyberpunkTheme).
     */
    function cyber_visits(): array
    {
        if (!cyber_ext()) {
            return ['today' => 0, 'total' => 0, 'available' => false];
        }

        return \Paymenter\Extensions\Others\CyberpunkTheme\Support\Visits::summary() + ['available' => true];
    }
}

if (!function_exists('cyber_invoice_services')) {
    /**
     * Servicios relacionados con una factura.
     *
     * Paymenter muestra las facturas por número, por lo que aquí resolvemos
     * a qué servidor/servicio pertenece cada línea para avisar al cliente.
     *
     * @return array<int, array{id:int,label:string,product:?string,status:?string}>
     */
    function cyber_invoice_services($invoice): array
    {
        if (!$invoice) {
            return [];
        }

        $services = [];

        foreach ($invoice->items as $item) {
            $service = null;

            try {
                if ($item->reference_type === \App\Models\Service::class) {
                    $service = \App\Models\Service::with('product')->find($item->reference_id);
                } elseif ($item->reference_type === \App\Models\ServiceUpgrade::class) {
                    $upgrade = \App\Models\ServiceUpgrade::find($item->reference_id);
                    $service = $upgrade ? \App\Models\Service::with('product')->find($upgrade->service_id) : null;
                }
            } catch (\Throwable $e) {
                $service = null;
            }

            if ($service && !isset($services[$service->id])) {
                $services[$service->id] = [
                    'id' => $service->id,
                    'label' => $service->label,
                    'product' => $service->product?->name,
                    'status' => $service->status,
                ];
            }
        }

        return array_values($services);
    }
}

if (!function_exists('cyber_quick_links')) {
    /**
     * Accesos rápidos del inicio. Si el administrador no configuró ninguno,
     * el sistema detecta automáticamente las categorías de la tienda.
     */
    function cyber_quick_links(): array
    {
        $configured = cyber_list('quick_links');

        $links = [];
        foreach ($configured as $link) {
            if (empty($link['label'])) {
                continue;
            }
            if (isset($link['enabled']) && !filter_var($link['enabled'], FILTER_VALIDATE_BOOLEAN)) {
                continue;
            }
            $links[] = [
                'label' => $link['label'],
                'description' => $link['description'] ?? null,
                'url' => $link['url'] ?? '#',
                'icon' => $link['icon'] ?? 'ri-flashlight-fill',
                'target' => $link['target'] ?? null,
            ];
        }

        if (count($links) > 0) {
            return $links;
        }

        // Auto-detección: categorías principales de la tienda.
        try {
            return \Illuminate\Support\Facades\Cache::remember('cyberpunk.quick_links.auto', now()->addMinutes(10), function () {
                return \App\Models\Category::whereNull('parent_id')
                    ->where(function ($query) {
                        $query->whereHas('children')->orWhereHas('products', fn ($q) => $q->where('hidden', false));
                    })
                    ->orderBy('sort')
                    ->take(6)
                    ->get()
                    ->map(fn ($category) => [
                        'label' => $category->name,
                        'description' => null,
                        'url' => route('category.show', ['category' => $category->slug]),
                        'icon' => 'ri-shopping-bag-3-fill',
                        'target' => null,
                    ])->toArray();
            });
        } catch (\Throwable $e) {
            return [];
        }
    }
}

if (!function_exists('cyber_pages')) {
    /**
     * Páginas personalizadas creadas desde el panel de administración.
     */
    function cyber_pages(bool $onlyNavigation = false): array
    {
        $pages = cyber_list('custom_pages');

        $pages = array_filter($pages, function ($page) use ($onlyNavigation) {
            if (empty($page['slug']) || empty($page['title'])) {
                return false;
            }
            if (isset($page['enabled']) && !filter_var($page['enabled'], FILTER_VALIDATE_BOOLEAN)) {
                return false;
            }
            if ($onlyNavigation) {
                return !isset($page['in_navigation']) || filter_var($page['in_navigation'], FILTER_VALIDATE_BOOLEAN);
            }

            return true;
        });

        usort($pages, fn ($a, $b) => ((int) ($a['sort'] ?? 0)) <=> ((int) ($b['sort'] ?? 0)));

        return array_values($pages);
    }
}

return [
    'name' => 'Cyberpunk',
    'author' => 'Sky Ultra Plus',
    'url' => 'https://skyultraplus.com',

    'settings' => [
        /*
        |----------------------------------------------------------------
        | General
        |----------------------------------------------------------------
        */
        [
            'name' => 'direct_checkout',
            'label' => 'Checkout directo',
            'type' => 'checkbox',
            'default' => false,
            'database_type' => 'boolean',
            'description' => 'No mostrar la página del producto, ir directo al checkout',
        ],
        [
            'name' => 'small_images',
            'label' => 'Imágenes pequeñas',
            'type' => 'checkbox',
            'default' => false,
            'database_type' => 'boolean',
            'description' => 'Mostrar imágenes pequeñas en el listado de productos',
        ],
        [
            'name' => 'show_category_description',
            'label' => 'Mostrar descripción de categoría',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'logo_display',
            'label' => 'Mostrar logo',
            'type' => 'select',
            'options' => [
                'logo-only' => 'Sólo logo',
                'logo-and-name' => 'Logo y nombre',
            ],
            'default' => 'logo-and-name',
        ],
        [
            'name' => 'home_page_text',
            'label' => 'Texto del inicio (markdown)',
            'type' => 'markdown',
            'default' => '',
            'description' => 'Texto opcional en markdown que se muestra debajo del marketing en el inicio.',
        ],

        /*
        |----------------------------------------------------------------
        | Efectos Cyberpunk
        |----------------------------------------------------------------
        */
        [
            'name' => 'effect_neon',
            'label' => 'Efecto neón',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
            'description' => 'Resplandor neón en bordes, botones y títulos',
        ],
        [
            'name' => 'effect_scanlines',
            'label' => 'Líneas de escaneo (scanlines)',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'effect_grid',
            'label' => 'Rejilla de fondo',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'effect_glitch',
            'label' => 'Glitch en títulos',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'effect_noise',
            'label' => 'Ruido / grano de pantalla',
            'type' => 'checkbox',
            'default' => false,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'font_family',
            'label' => 'Tipografía',
            'type' => 'select',
            'options' => [
                'system' => 'Sistema (rápida)',
                'orbitron' => 'Orbitron (futurista)',
                'rajdhani' => 'Rajdhani (tecnológica)',
                'share-tech' => 'Share Tech Mono (terminal)',
            ],
            'default' => 'system',
        ],
        [
            'name' => 'background_image',
            'label' => 'Imagen de fondo',
            'type' => 'text',
            'default' => '',
            'description' => 'Ruta o URL de la imagen de fondo del sitio (se configura mejor desde Cyberpunk Theme).',
        ],
        [
            'name' => 'background_overlay',
            'label' => 'Opacidad de la capa sobre el fondo (%)',
            'type' => 'number',
            'default' => 80,
            'database_type' => 'integer',
            'min_value' => 0,
            'max_value' => 100,
        ],

        /*
        |----------------------------------------------------------------
        | Secciones del inicio
        |----------------------------------------------------------------
        */
        [
            'name' => 'banner_enabled',
            'label' => 'Mostrar banner',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'banner_interval',
            'label' => 'Intervalo del banner (ms)',
            'type' => 'number',
            'default' => 6000,
            'database_type' => 'integer',
        ],
        [
            'name' => 'marketing_enabled',
            'label' => 'Mostrar bloques de marketing',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'stats_enabled',
            'label' => 'Mostrar estadísticas (clientes, servidores...)',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'uptime_enabled',
            'label' => 'Mostrar contador de tiempo activo',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'visitors_enabled',
            'label' => 'Mostrar contador de visitas',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'quick_links_enabled',
            'label' => 'Mostrar accesos rápidos',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'socials_enabled',
            'label' => 'Mostrar redes sociales',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],

        /*
        |----------------------------------------------------------------
        | Comunidad y reseñas
        |----------------------------------------------------------------
        */
        [
            'name' => 'marketing_align',
            'label' => 'Posición del marketing',
            'type' => 'select',
            'options' => [
                'left' => 'A la izquierda',
                'center' => 'En el centro',
                'right' => 'A la derecha',
            ],
            'default' => 'left',
        ],
        [
            'name' => 'reviews_page_enabled',
            'label' => 'Activar el apartado de reseñas',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'reviews_name',
            'label' => 'Nombre del apartado de reseñas',
            'type' => 'text',
            'default' => 'Reseñas',
        ],
        [
            'name' => 'reviews_slug',
            'label' => 'URL del apartado de reseñas',
            'type' => 'text',
            'default' => 'resenas',
        ],
        [
            'name' => 'community_enabled',
            'label' => 'Activar comunidad',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'community_name',
            'label' => 'Nombre de la comunidad',
            'type' => 'text',
            'default' => 'Comunidad',
        ],
        [
            'name' => 'reviews_enabled',
            'label' => 'Activar valoraciones y comentarios en productos',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],
        [
            'name' => 'avatar_uploads_enabled',
            'label' => 'Permitir subir avatar personalizado',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
        ],

        /*
        |----------------------------------------------------------------
        | Redes sociales
        |----------------------------------------------------------------
        */
        ['name' => 'social_facebook', 'label' => 'Facebook', 'type' => 'text', 'default' => ''],
        ['name' => 'social_discord', 'label' => 'Discord', 'type' => 'text', 'default' => ''],
        ['name' => 'social_instagram', 'label' => 'Instagram', 'type' => 'text', 'default' => ''],
        ['name' => 'social_whatsapp_channel', 'label' => 'Canal de WhatsApp', 'type' => 'text', 'default' => ''],
        ['name' => 'social_whatsapp_group', 'label' => 'Grupo de WhatsApp', 'type' => 'text', 'default' => ''],
        ['name' => 'social_telegram', 'label' => 'Telegram', 'type' => 'text', 'default' => ''],
        ['name' => 'social_youtube', 'label' => 'YouTube', 'type' => 'text', 'default' => ''],
        ['name' => 'social_tiktok', 'label' => 'TikTok', 'type' => 'text', 'default' => ''],
        ['name' => 'social_x', 'label' => 'X / Twitter', 'type' => 'text', 'default' => ''],
        ['name' => 'social_github', 'label' => 'GitHub', 'type' => 'text', 'default' => ''],

        /*
        |----------------------------------------------------------------
        | Colores (Claro)
        |----------------------------------------------------------------
        */
        [
            'name' => 'primary',
            'label' => 'Primario - Color de marca (Claro)',
            'type' => 'color',
            'default' => 'hsl(217, 91%, 50%)',
        ],
        [
            'name' => 'secondary',
            'label' => 'Secundario - Color de marca (Claro)',
            'type' => 'color',
            'default' => 'hsl(330, 90%, 55%)',
        ],
        [
            'name' => 'accent',
            'label' => 'Acento - Neón (Claro)',
            'type' => 'color',
            'default' => 'hsl(199, 92%, 48%)',
        ],
        [
            'name' => 'neutral',
            'label' => 'Bordes y acentos (Claro)',
            'type' => 'color',
            'default' => 'hsl(214, 32%, 85%)',
        ],
        [
            'name' => 'base',
            'label' => 'Base - Color de texto (Claro)',
            'type' => 'color',
            'default' => 'hsl(222, 44%, 12%)',
        ],
        [
            'name' => 'muted',
            'label' => 'Apagado - Color de texto (Claro)',
            'type' => 'color',
            'default' => 'hsl(215, 16%, 42%)',
        ],
        [
            'name' => 'inverted',
            'label' => 'Invertido - Color de texto (Claro)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 100%)',
        ],
        [
            'name' => 'background',
            'label' => 'Fondo (Claro)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 100%)',
        ],
        [
            'name' => 'background-secondary',
            'label' => 'Fondo secundario (Claro)',
            'type' => 'color',
            'default' => 'hsl(214, 45%, 97%)',
        ],

        /*
        |----------------------------------------------------------------
        | Colores (Oscuro) — paleta por defecto: rosa + rojo + negro
        |----------------------------------------------------------------
        */
        [
            'name' => 'dark-primary',
            'label' => 'Primario - Color de marca (Oscuro)',
            'type' => 'color',
            'default' => 'hsl(217, 91%, 60%)',
        ],
        [
            'name' => 'dark-secondary',
            'label' => 'Secundario - Color de marca (Oscuro)',
            'type' => 'color',
            'default' => 'hsl(330, 100%, 62%)',
        ],
        [
            'name' => 'dark-accent',
            'label' => 'Acento - Neón (Oscuro)',
            'type' => 'color',
            'default' => 'hsl(199, 95%, 60%)',
        ],
        [
            'name' => 'dark-neutral',
            'label' => 'Bordes y acentos (Oscuro)',
            'type' => 'color',
            'default' => 'hsl(217, 33%, 24%)',
        ],
        [
            'name' => 'dark-base',
            'label' => 'Base - Color de texto (Oscuro)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 100%)',
        ],
        [
            'name' => 'dark-muted',
            'label' => 'Apagado - Color de texto (Oscuro)',
            'type' => 'color',
            'default' => 'hsl(215, 22%, 72%)',
        ],
        [
            'name' => 'dark-inverted',
            'label' => 'Invertido - Color de texto (Oscuro)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 100%)',
        ],
        [
            'name' => 'dark-background',
            'label' => 'Fondo (Oscuro)',
            'type' => 'color',
            'default' => 'hsl(222, 47%, 6%)',
        ],
        [
            'name' => 'dark-background-secondary',
            'label' => 'Fondo secundario (Oscuro)',
            'type' => 'color',
            'default' => 'hsl(222, 40%, 10%)',
        ],
    ],
];
