<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Extension;
use App\Helpers\ExtensionHelper;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Paymenter\Extensions\Others\CyberpunkTheme\Http\Middleware\CountVisit;
use Paymenter\Extensions\Others\CyberpunkTheme\Livewire\Avatar as AvatarComponent;
use Paymenter\Extensions\Others\CyberpunkTheme\Livewire\Community as CommunityComponent;
use Paymenter\Extensions\Others\CyberpunkTheme\Livewire\CommunityPreview as CommunityPreviewComponent;
use Paymenter\Extensions\Others\CyberpunkTheme\Livewire\CustomPage as CustomPageComponent;
use Paymenter\Extensions\Others\CyberpunkTheme\Livewire\ProductReviews as ProductReviewsComponent;
use Paymenter\Extensions\Others\CyberpunkTheme\Livewire\Reviews as ReviewsComponent;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Installer;

#[ExtensionMeta(
    name: 'Cyberpunk Theme',
    description: 'Tema Cyberpunk totalmente personalizable para Paymenter: banner con marketing rotativo, animaciones de fondo, comunidad de usuarios, reseñas con estrellas de los planes y del servicio, avatares, contadores y paletas de colores.',
    version: '1.4.0',
    author: 'Sky Ultra Plus',
    url: 'https://skyultraplus.com',
    icon: 'ri-cpu-line',
)]
class CyberpunkTheme extends Extension
{
    public function __construct(public $config = []) {}

    /**
     * Ajustes propios de la extensión (los del tema se editan en su
     * propia página "Cyberpunk Theme" del panel de administración).
     */
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'community_enabled',
                'label' => 'Activar comunidad',
                'type' => 'checkbox',
                'default' => true,
                'description' => 'Publicaciones, likes y comentarios de los usuarios.',
            ],
            [
                'name' => 'reviews_enabled',
                'label' => 'Activar reseñas en productos',
                'type' => 'checkbox',
                'default' => true,
                'description' => 'Los clientes puntúan con estrellas y escriben su reseña.',
            ],
            [
                'name' => 'count_visits',
                'label' => 'Contar visitas',
                'type' => 'checkbox',
                'default' => true,
                'description' => 'Registra visitas únicas por día para el contador del inicio.',
            ],
            [
                'name' => 'auto_moderate',
                'label' => 'Aprobar publicaciones automáticamente',
                'type' => 'checkbox',
                'default' => true,
                'description' => 'Si se desactiva, las publicaciones necesitan aprobación del administrador.',
            ],
        ];
    }

    /**
     * Instalación: migraciones + copia del tema + ajustes por defecto.
     */
    public function installed()
    {
        // Creamos las tablas directamente (no dependemos de que el runner de
        // migraciones haya podido completarlas en instalaciones anteriores).
        Support\Database::ensureTables();

        ExtensionHelper::runMigrations('extensions/Others/CyberpunkTheme/database/migrations');

        // No sobreescribimos ajustes existentes: installed() puede llamarse
        // más de una vez (subida del ZIP + instalación desde el panel).
        Installer::install(overwriteSettings: false);
    }

    public function upgraded($oldVersion = null)
    {
        Support\Database::ensureTables();

        ExtensionHelper::runMigrations('extensions/Others/CyberpunkTheme/database/migrations');

        Installer::install(overwriteSettings: false);
    }

    /**
     * Al desinstalar NO se borra nada.
     *
     * Actualizar la extensión pasa por desinstalar la versión vieja e
     * instalar la nueva, así que si aquí se hiciera rollback de las
     * migraciones se perderían las publicaciones, los comentarios, los likes,
     * los avatares y los contadores en cada actualización.
     *
     * Los datos se quedan en su sitio y la instalación siguiente los reutiliza.
     * Para borrarlos de verdad hay que hacerlo a mano en la base de datos
     * (tablas ext_cyberpunk_*).
     */
    public function uninstalled()
    {
        // Intencionadamente vacío: no se borra ninguna tabla ni ningún ajuste.
    }

    public function boot()
    {
        // El tema comprueba esta bandera para saber si puede usar la comunidad,
        // las reseñas y los avatares (helper cyber_ext()).
        config(['cyberpunk.booted' => true]);

        View::addNamespace('cyberpunk', __DIR__ . '/resources/views');

        require __DIR__ . '/routes/web.php';

        // Ojo: hay que usar los nombres completos con alias. Escribir
        // Livewire\Community::class aquí resolvería a Livewire\Livewire\Community,
        // porque "Livewire" ya está importado como Livewire\Livewire.
        Livewire::component('cyberpunk.community', CommunityComponent::class);
        Livewire::component('cyberpunk.community-preview', CommunityPreviewComponent::class);
        Livewire::component('cyberpunk.product-reviews', ProductReviewsComponent::class);
        Livewire::component('cyberpunk.avatar', AvatarComponent::class);
        Livewire::component('cyberpunk.custom-page', CustomPageComponent::class);
        Livewire::component('cyberpunk.reviews', ReviewsComponent::class);

        if (Config::bool('count_visits', true)) {
            ExtensionHelper::registerMiddleware(CountVisit::class);
        }

        // Enlaces en la barra de navegación: páginas personalizadas + comunidad
        Event::listen('navigation', function () {
            $links = [];

            if (function_exists('cyber_pages')) {
                foreach (cyber_pages(true) as $page) {
                    $links[] = [
                        'name' => $page['title'],
                        'url' => url('/p/' . $page['slug']),
                        'icon' => $page['icon'] ?? 'ri-file-text',
                        'priority' => 40 + (int) ($page['sort'] ?? 0),
                    ];
                }
            }

            if (Config::themeBool('community_enabled', true)) {
                $links[] = [
                    'name' => Config::theme('community_name', 'Comunidad'),
                    'url' => url('/' . Config::communitySlug()),
                    'icon' => 'ri-chat-smile-2',
                    'priority' => 35,
                ];
            }

            if (Config::themeBool('reviews_page_enabled', true)) {
                $links[] = [
                    'name' => Config::theme('reviews_name', 'Reseñas'),
                    'url' => url('/' . Config::reviewsSlug()),
                    'icon' => 'ri-star-smile',
                    'priority' => 36,
                ];
            }

            return $links;
        });

        // Enlace también en la barra lateral del cliente
        Event::listen('navigation.dashboard', function () {
            if (!Config::themeBool('community_enabled', true)) {
                return [];
            }

            return [
                [
                    'name' => Config::theme('community_name', 'Comunidad'),
                    'url' => url('/' . Config::communitySlug()),
                    'icon' => 'ri-chat-smile-2',
                    'priority' => 45,
                ],
                [
                    'name' => Config::theme('reviews_name', 'Reseñas'),
                    'url' => url('/' . Config::reviewsSlug()),
                    'icon' => 'ri-star-smile',
                    'priority' => 46,
                ],
            ];
        });

        $permissions = [
            'admin.cyberpunk.view' => 'Ver configuración del tema Cyberpunk',
            'admin.cyberpunk.update' => 'Editar configuración del tema Cyberpunk',
            'admin.cyberpunk.moderate' => 'Moderar la comunidad del tema Cyberpunk',
        ];

        Event::listen('permissions', fn () => $permissions);
        Event::listen('api.permissions', fn () => $permissions);
    }
}
