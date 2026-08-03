<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Admin\Pages;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Color\Factory as ColorFactory;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Database;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Defaults;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Icons;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Installer;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Palettes;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews as ReviewsHelper;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Visits;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Comment;

/**
 * Página de personalización completa del tema Cyberpunk.
 */
class CyberpunkThemePage extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected string $view = 'cyberpunk::admin.theme-page';

    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?string $navigationLabel = 'Cyberpunk Theme';

    protected static ?string $title = 'Cyberpunk Theme';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-cpu-line';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public string $palette = '';

    public function mount(): void
    {
        $this->form->fill(Config::all());
    }

    // ------------------------------------------------------------------
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('cyberpunk')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        $this->generalTab(),
                        $this->appearanceTab(),
                        $this->bannerTab(),
                        $this->marketingTab(),
                        $this->pagesTab(),
                        $this->communityTab(),
                        $this->socialsTab(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function generalTab(): Tab
    {
        return Tab::make('General')
            ->icon('ri-settings-3-line')
            ->schema([
                Section::make('Secciones del inicio')
                    ->description('Activa o desactiva cada bloque de la página principal.')
                    ->columns(3)
                    ->schema([
                        Toggle::make('banner_enabled')->label('Banner principal'),
                        Toggle::make('marketing_enabled')->label('Bloques de marketing'),
                        Toggle::make('marquee_enabled')
                            ->label('Cinta de frases en movimiento')
                            ->helperText('La tira que va pasando frases bajo el banner.'),
                        Toggle::make('stats_enabled')->label('Estadísticas'),
                        Toggle::make('uptime_enabled')->label('Contador de tiempo activo'),
                        Toggle::make('visitors_enabled')->label('Contador de visitas'),
                        Toggle::make('quick_links_enabled')->label('Accesos rápidos'),
                        Toggle::make('socials_enabled')->label('Redes sociales'),
                        Toggle::make('community_enabled')->label('Comunidad'),
                        Toggle::make('reviews_enabled')->label('Reseñas en productos'),
                    ]),

                Section::make('Tienda')
                    ->columns(3)
                    ->schema([
                        Toggle::make('direct_checkout')
                            ->label('Checkout directo')
                            ->helperText('Saltar la página del producto.'),
                        Toggle::make('small_images')
                            ->label('Imágenes pequeñas')
                            ->helperText('Sólo se aplica al modo recortado.'),
                        Toggle::make('show_category_description')->label('Descripción de categorías'),
                        Select::make('image_mode')
                            ->label('Imágenes de categorías y productos')
                            ->options([
                                'full' => 'Completas — se adaptan al tamaño de la imagen',
                                'cover' => 'Recortadas a una altura fija',
                            ])
                            ->columnSpan(2)
                            ->helperText('En modo "completas" se ve toda la imagen (por ejemplo 1080x720) y el texto lleva un degradado detrás para que se lea bien.'),
                    ]),

                Section::make('Contadores')
                    ->description('El tiempo activo y las visitas se guardan en la base de datos: no se reinician al reiniciar el servidor ni al actualizar el tema.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('uptime_start')
                            ->label('Contar el tiempo activo desde')
                            ->placeholder('2024-05-17')
                            ->helperText('Fecha de apertura de tu hosting. Se rellenó sola al instalar; cámbiala sólo si quieres otra. Formato AAAA-MM-DD.')
                            ->rule(function () {
                                return function (string $attribute, $value, \Closure $fail) {
                                    if (trim((string) $value) === '') {
                                        return;
                                    }

                                    try {
                                        \Illuminate\Support\Carbon::parse($value);
                                    } catch (\Throwable $e) {
                                        $fail('No entiendo esa fecha. Usa el formato AAAA-MM-DD.');
                                    }
                                };
                            }),
                        \Filament\Forms\Components\Placeholder::make('visitas_actuales')
                            ->label('Visitas contadas hasta ahora')
                            ->content(function (): string {
                                $v = Visits::summary();

                                return 'Hoy: ' . number_format($v['today']) . ' · Total: ' . number_format($v['total'])
                                    . ' — el total se guarda aparte y sólo lo pone a cero el botón «Reiniciar visitas».';
                            }),
                    ]),

                Section::make('Textos')
                    ->columns(2)
                    ->schema([
                        Select::make('logo_display')
                            ->label('Mostrar logo')
                            ->options([
                                'logo-and-name' => 'Logo y nombre',
                                'logo-only' => 'Sólo logo',
                            ]),
                        TextInput::make('footer_text')
                            ->label('Texto del pie de página')
                            ->maxLength(255),
                        Textarea::make('home_page_text')
                            ->label('Texto extra del inicio (markdown)')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Opcional. Se muestra debajo del marketing.'),
                    ]),
            ]);
    }

    protected function appearanceTab(): Tab
    {
        return Tab::make('Apariencia')
            ->icon('ri-palette-line')
            ->schema([
                Section::make('Paletas de colores')
                    ->description('Un clic aplica y guarda la paleta al instante. Debajo puedes ajustar cualquier color a mano.')
                    ->schema([
                        Actions::make(
                            collect(Palettes::all())->map(
                                fn (array $palette, string $key) => Action::make('palette_' . str_replace('-', '_', $key))
                                    ->label($palette['label'])
                                    ->icon('ri-palette-line')
                                    ->color('gray')
                                    ->action(fn () => $this->applyPalette($key))
                            )->values()->all()
                        ),
                    ]),

                Section::make('Animaciones de fondo')
                    ->description('Se pueden combinar varias a la vez, y cada modo puede llevar las suyas.')
                    ->columns(2)
                    ->schema([
                        CheckboxList::make('anim_dark')
                            ->label('Modo oscuro')
                            ->options($this->animationOptions())
                            ->columns(1)
                            ->bulkToggleable(),
                        CheckboxList::make('anim_light')
                            ->label('Modo claro')
                            ->options($this->animationOptions())
                            ->columns(1)
                            ->bulkToggleable(),
                    ]),

                Section::make('Colores — modo oscuro')
                    ->description('El tema Cyberpunk está pensado para el modo oscuro.')
                    ->columns(3)
                    ->schema([
                        $this->colorField('dark-primary', 'Primario'),
                        $this->colorField('dark-secondary', 'Secundario'),
                        $this->colorField('dark-accent', 'Acento (neón)'),
                        $this->colorField('dark-background', 'Fondo'),
                        $this->colorField('dark-background-secondary', 'Fondo secundario'),
                        $this->colorField('dark-neutral', 'Bordes'),
                        $this->colorField('dark-base', 'Texto'),
                        $this->colorField('dark-muted', 'Texto apagado'),
                        $this->colorField('dark-inverted', 'Texto invertido'),
                    ]),

                Section::make('Colores — modo claro')
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        $this->colorField('primary', 'Primario'),
                        $this->colorField('secondary', 'Secundario'),
                        $this->colorField('accent', 'Acento (neón)'),
                        $this->colorField('background', 'Fondo'),
                        $this->colorField('background-secondary', 'Fondo secundario'),
                        $this->colorField('neutral', 'Bordes'),
                        $this->colorField('base', 'Texto'),
                        $this->colorField('muted', 'Texto apagado'),
                        $this->colorField('inverted', 'Texto invertido'),
                    ]),

                Section::make('Efectos y fondo')
                    ->columns(3)
                    ->schema([
                        Toggle::make('effect_neon')->label('Resplandor neón'),
                        Toggle::make('effect_scanlines')->label('Líneas de escaneo'),
                        Toggle::make('effect_grid')->label('Rejilla de fondo'),
                        Toggle::make('effect_glitch')->label('Glitch en títulos'),
                        Toggle::make('effect_noise')->label('Ruido de pantalla'),
                        Select::make('font_family')
                            ->label('Tipografía')
                            ->options([
                                'system' => 'Sistema (más rápida)',
                                'orbitron' => 'Orbitron (futurista)',
                                'rajdhani' => 'Rajdhani (tecnológica)',
                                'share-tech' => 'Share Tech Mono (terminal)',
                            ]),
                        FileUpload::make('background_image')
                            ->label('Imagen de fondo del sitio')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('cyberpunk/backgrounds')
                            ->columnSpan(2)
                            ->helperText('Opcional. Déjalo vacío para usar sólo el color de fondo.'),
                        TextInput::make('background_overlay')
                            ->label('Opacidad de la capa (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->helperText('100 = fondo sólido, 0 = imagen totalmente visible.'),
                    ]),
            ]);
    }

    /**
     * Opciones de animaciones disponibles (las define el tema).
     *
     * @return array<string, string>
     */
    protected function animationOptions(): array
    {
        Defaults::loadThemeHelpers();

        if (function_exists('cyber_animations')) {
            return cyber_animations();
        }

        return [
            'stars' => 'Estrellas',
            'shooting' => 'Estrellas fugaces',
            'planets' => 'Planetas',
            'clouds' => 'Nubes',
            'rain' => 'Lluvia',
            'storm' => 'Truenos',
            'snow' => 'Nieve',
            'matrix' => 'Lluvia digital',
            'aurora' => 'Aurora',
        ];
    }

    /**
     * Aplica una paleta: rellena el formulario y la guarda al momento,
     * para que el cambio se vea en la web sin pulsar nada más.
     */
    public function applyPalette(string $key): void
    {
        $this->authorizeUpdate();

        $colors = Palettes::colors($key);

        if (count($colors) === 0) {
            Notification::make()->title('Paleta desconocida')->danger()->send();

            return;
        }

        // Actualizamos el estado del formulario (los selectores de color
        // se refrescan solos) y guardamos sólo las claves de color.
        $this->data = array_merge($this->data ?? [], $colors);

        Config::save($colors);

        Notification::make()
            ->title('Paleta aplicada')
            ->body(Palettes::all()[$key]['label'] . ' — ya está activa en la web.')
            ->success()
            ->send();
    }

    /**
     * Selector visual de iconos: se ven dibujados y se pueden buscar por
     * nombre entre todos los iconos que trae Paymenter (Remix Icon).
     */
    protected function iconField(string $name = 'icon', string $default = 'ri-server-fill'): Select
    {
        return Select::make($name)
            ->label('Icono')
            ->default($default)
            ->native(false)
            ->searchable()
            ->allowHtml()
            ->searchPrompt('Escribe para buscar: servidor, bot, nube, whatsapp...')
            ->searchingMessage('Buscando iconos...')
            ->noSearchResultsMessage('Ningún icono coincide con esa búsqueda.')
            ->options(fn () => Icons::options())
            ->getSearchResultsUsing(fn (string $search) => Icons::options($search))
            ->getOptionLabelUsing(fn ($value) => Icons::label($value))
            ->helperText('Elige uno de la lista o busca por nombre en inglés (server, cloud, robot, whatsapp...).');
    }

    /**
     * Selector de color que siempre guarda el valor en formato hsl(),
     * que es el que usan las variables CSS del tema.
     */
    protected function colorField(string $name, string $label): ColorPicker
    {
        return ColorPicker::make($name)
            ->label($label)
            ->hsl()
            ->live()
            ->rules([
                function () {
                    return function (string $attribute, $value, \Closure $fail) {
                        if ($value === null || $value === '') {
                            return;
                        }

                        try {
                            ColorFactory::fromString(trim((string) $value));
                        } catch (\Exception $e) {
                            $fail('El color no es válido.');
                        }
                    };
                },
            ])
            ->afterStateUpdated(function ($state, callable $set) use ($name) {
                if ($state === null || $state === '') {
                    return;
                }

                try {
                    $set($name, preg_replace('/,\s*/', ', ', ColorFactory::fromString(trim((string) $state))->toHsl()->__toString()));
                } catch (\Exception $e) {
                    // Dejamos el valor tal cual si no se puede convertir.
                }
            });
    }

    protected function bannerTab(): Tab
    {
        return Tab::make('Banner')
            ->icon('ri-slideshow-line')
            ->schema([
                Section::make('Ajustes del banner')
                    ->description('El banner se activa o desactiva desde la pestaña General.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('banner_interval')
                            ->label('Cambio de imagen cada (ms)')
                            ->numeric()
                            ->minValue(1500)
                            ->helperText('6000 = 6 segundos.'),
                    ]),

                Section::make('Diapositivas')
                    ->description('Cada diapositiva puede tener su propia imagen, título, texto y botón.')
                    ->schema([
                        Repeater::make('banner_slides')
                            ->hiddenLabel()
                            ->columns(2)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Diapositiva')
                            ->defaultItems(1)
                            ->addActionLabel('Añadir diapositiva')
                            ->schema([
                                Toggle::make('enabled')->label('Activa')->default(true),
                                FileUpload::make('image')
                                    ->label('Imagen')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('cyberpunk/banner'),
                                TextInput::make('title')->label('Título')->maxLength(120),
                                Textarea::make('subtitle')->label('Texto')->rows(2)->maxLength(300),
                                TextInput::make('button_label')->label('Texto del botón')->maxLength(60),
                                TextInput::make('button_url')
                                    ->label('URL del botón')
                                    ->helperText('Vacío = el sistema detecta la tienda automáticamente.'),
                            ]),
                    ]),

                Section::make('Palabras de marketing en movimiento')
                    ->description('Se muestran rotando dentro del banner. La cinta deslizante que las repite es opcional y se activa en la pestaña General.')
                    ->schema([
                        Repeater::make('marketing_words')
                            ->hiddenLabel()
                            ->simple(
                                TextInput::make('text')->label('Frase')->required()->maxLength(120)
                            )
                            ->reorderable()
                            ->addActionLabel('Añadir frase'),
                    ]),
            ]);
    }

    protected function marketingTab(): Tab
    {
        return Tab::make('Marketing')
            ->icon('ri-megaphone-line')
            ->schema([
                Section::make('Encabezado y posición')
                    ->columns(3)
                    ->schema([
                        TextInput::make('marketing_title')->label('Título')->maxLength(150),
                        TextInput::make('marketing_subtitle')->label('Subtítulo')->maxLength(255),
                        Select::make('marketing_align')
                            ->label('Posición del marketing')
                            ->options([
                                'left' => 'A la izquierda',
                                'center' => 'En el centro',
                                'right' => 'A la derecha',
                            ])
                            ->helperText('Afecta al banner y a los títulos de las secciones.'),
                    ]),

                Section::make('Tarjetas de servicios')
                    ->description('Se muestran ANTES de los productos, en la página de inicio.')
                    ->schema([
                        Repeater::make('marketing_cards')
                            ->hiddenLabel()
                            ->columns(2)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Tarjeta')
                            ->addActionLabel('Añadir tarjeta')
                            ->schema([
                                Toggle::make('enabled')->label('Activa')->default(true),
                                $this->iconField('icon', 'ri-server-fill'),
                                TextInput::make('title')->label('Título')->required()->maxLength(80),
                                TextInput::make('url')->label('Enlace (opcional)'),
                                Textarea::make('description')->label('Descripción')->rows(2)->columnSpanFull()->maxLength(255),
                            ]),
                    ]),

                Section::make('Ventajas')
                    ->description('Fila de ventajas cortas debajo de las tarjetas.')
                    ->schema([
                        Repeater::make('features')
                            ->hiddenLabel()
                            ->columns(3)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Ventaja')
                            ->addActionLabel('Añadir ventaja')
                            ->schema([
                                $this->iconField('icon', 'ri-check-double-fill'),
                                TextInput::make('title')->label('Título')->required()->maxLength(80),
                                TextInput::make('description')->label('Descripción')->maxLength(150),
                            ]),
                    ]),

                Section::make('Accesos rápidos')
                    ->description('Si lo dejas vacío, el sistema detecta y enlaza automáticamente las categorías de la tienda.')
                    ->schema([
                        Repeater::make('quick_links')
                            ->hiddenLabel()
                            ->columns(2)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Acceso')
                            ->addActionLabel('Añadir acceso rápido')
                            ->schema([
                                Toggle::make('enabled')->label('Activo')->default(true),
                                $this->iconField('icon', 'ri-flashlight-fill'),
                                TextInput::make('label')->label('Nombre')->required()->maxLength(80),
                                TextInput::make('url')->label('URL')->required(),
                                TextInput::make('description')->label('Descripción')->maxLength(120),
                                Select::make('target')
                                    ->label('Abrir en')
                                    ->options(['' => 'Misma pestaña', '_blank' => 'Pestaña nueva']),
                            ]),
                    ]),
            ]);
    }

    protected function pagesTab(): Tab
    {
        return Tab::make('Páginas')
            ->icon('ri-pages-line')
            ->schema([
                Section::make('Páginas personalizadas')
                    ->description('Crea páginas nuevas con tu propio HTML. Aparecerán en la barra de navegación y estarán disponibles en /p/tu-slug')
                    ->schema([
                        Repeater::make('custom_pages')
                            ->hiddenLabel()
                            ->columns(2)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Página')
                            ->addActionLabel('Añadir página')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Nombre')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(80)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if (!$get('slug')) {
                                            $set('slug', Str::slug((string) $state));
                                        }
                                    }),
                                TextInput::make('slug')
                                    ->label('Slug (URL)')
                                    ->required()
                                    ->prefix(url('/p') . '/')
                                    ->rule('regex:/^[A-Za-z0-9_-]+$/')
                                    ->maxLength(60),
                                Toggle::make('enabled')->label('Publicada')->default(true),
                                Toggle::make('in_navigation')->label('Mostrar en la barra de navegación')->default(true),
                                Toggle::make('show_title')->label('Mostrar el título en la página')->default(true),
                                $this->iconField('icon', 'ri-file-text-fill'),
                                TextInput::make('sort')->label('Orden')->numeric()->default(0),
                                TextInput::make('description')->label('Descripción corta')->maxLength(200),
                                RichEditor::make('content')
                                    ->label('Contenido')
                                    ->columnSpanFull()
                                    ->helperText('Puedes escribir texto con formato o pegar HTML directamente.'),
                            ]),
                    ]),
            ]);
    }

    protected function communityTab(): Tab
    {
        return Tab::make('Comunidad')
            ->icon('ri-chat-smile-2-line')
            ->schema([
                Section::make('Sección de comunidad')
                    ->description('La comunidad y las reseñas se activan o desactivan desde la pestaña General.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('avatar_uploads_enabled')->label('Permitir avatares personalizados'),
                        TextInput::make('community_name')
                            ->label('Nombre de la sección')
                            ->maxLength(60)
                            ->helperText('Aparece en la barra de navegación.'),
                        TextInput::make('community_slug')
                            ->label('URL de la sección')
                            ->prefix(url('/') . '/')
                            ->rule('regex:/^[A-Za-z0-9_-]+$/')
                            ->maxLength(60)
                            ->helperText('Al cambiarla se actualiza la ruta pública.'),
                        Textarea::make('community_description')
                            ->label('Descripción')
                            ->rows(2)
                            ->columnSpanFull()
                            ->maxLength(255),
                        TextInput::make('community_media_limit')
                            ->label('Máximo de archivos por publicación')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->helperText($this->uploadLimitsHint()),
                    ]),

                Section::make('Apartado de reseñas')
                    ->description('Una página propia donde los clientes puntúan con estrellas y cuentan su experiencia. Tiene dos partes: las reseñas de cada plan y la opinión sobre el servicio en general. Para poner estrellas hay que escribir una reseña.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('reviews_page_enabled')
                            ->label('Activar el apartado de reseñas')
                            ->columnSpanFull(),
                        TextInput::make('reviews_name')
                            ->label('Nombre del apartado')
                            ->maxLength(60)
                            ->helperText('Por ejemplo: Reseñas, Opiniones, Valoraciones...'),
                        TextInput::make('reviews_slug')
                            ->label('URL del apartado')
                            ->prefix(url('/') . '/')
                            ->rule('regex:/^[A-Za-z0-9_-]+$/')
                            ->maxLength(60),
                        Textarea::make('reviews_description')
                            ->label('Descripción')
                            ->rows(2)
                            ->columnSpanFull()
                            ->maxLength(255),
                    ]),

                Section::make('Opinión sobre el servicio en general')
                    ->description('La segunda pestaña del apartado de reseñas: lo que opinan los clientes del hosting en conjunto, no de un plan concreto.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('general_reviews_name')
                            ->label('Nombre de esta pestaña')
                            ->maxLength(60)
                            ->helperText('Por ejemplo: El servicio en general, Nuestro hosting, La experiencia...'),
                        Textarea::make('general_reviews_description')
                            ->label('Descripción')
                            ->rows(2)
                            ->maxLength(255),
                    ]),

                Section::make('Reseñas destacadas en el inicio')
                    ->description('Se muestran en la página principal justo después de los planes y antes de la comunidad. Puedes elegir a mano las mejores reseñas, tanto de un plan como de la opinión general.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('featured_reviews_enabled')
                            ->label('Mostrar reseñas destacadas en el inicio')
                            ->columnSpanFull(),
                        TextInput::make('featured_reviews_title')
                            ->label('Título de la sección')
                            ->maxLength(120),
                        TextInput::make('featured_reviews_subtitle')
                            ->label('Subtítulo')
                            ->maxLength(200),
                        TextInput::make('featured_reviews_limit')
                            ->label('Cuántas mostrar')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12),
                        Select::make('featured_reviews_mode')
                            ->label('Qué reseñas mostrar')
                            ->options([
                                'mixed' => 'Las que yo elija y, si faltan, las mejores',
                                'manual' => 'Sólo las que yo elija',
                            ]),
                        Actions::make([
                            Action::make('chooseFeaturedReviews')
                                ->label('Elegir las reseñas destacadas')
                                ->icon('ri-award-fill')
                                ->color('primary')
                                ->modalHeading('Elegir las reseñas del inicio')
                                ->modalDescription('Marca las reseñas que quieres enseñar en la página principal. Se listan las mejor valoradas primero.')
                                ->modalSubmitActionLabel('Guardar selección')
                                ->fillForm(fn (): array => [
                                    'ids' => Comment::reviews()->where('featured', true)->pluck('id')->all(),
                                ])
                                ->schema([
                                    CheckboxList::make('ids')
                                        ->hiddenLabel()
                                        ->options(fn (): array => $this->featuredReviewOptions())
                                        ->columns(1)
                                        ->bulkToggleable()
                                        ->searchable()
                                        ->noSearchResultsMessage('Ninguna reseña coincide.'),
                                ])
                                ->action(function (array $data) {
                                    $this->authorizeUpdate();

                                    $ids = array_map('intval', $data['ids'] ?? []);

                                    Comment::query()->where('featured', true)->update(['featured' => false]);

                                    if (count($ids) > 0) {
                                        Comment::whereIn('id', $ids)->update(['featured' => true]);
                                    }

                                    ReviewsHelper::flush();

                                    Notification::make()
                                        ->title(count($ids) > 0
                                            ? count($ids) . ' reseña(s) destacadas'
                                            : 'Ya no hay reseñas destacadas')
                                        ->body(count($ids) > 0
                                            ? 'Ya se ven en la página de inicio.'
                                            : 'En modo "las que yo elija" la sección desaparecerá del inicio.')
                                        ->success()
                                        ->send();
                                }),
                        ])->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Reseñas disponibles para destacar, las mejor valoradas primero.
     *
     * @return array<int, string>
     */
    protected function featuredReviewOptions(int $limit = 60): array
    {
        try {
            return Comment::reviews()
                ->with('user')
                ->orderByDesc('featured')
                ->orderByDesc('rating')
                ->orderByDesc('created_at')
                ->take($limit)
                ->get()
                ->mapWithKeys(function (Comment $review) {
                    $estrellas = str_repeat('★', (int) $review->rating)
                        . str_repeat('☆', 5 - (int) $review->rating);

                    $autor = $review->user?->name ?? 'Usuario';
                    $texto = Str::limit(preg_replace('/\s+/', ' ', (string) $review->content), 90);

                    return [$review->id => "{$estrellas}  {$autor} · {$review->targetLabel()} — {$texto}"];
                })
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Los archivos se suben de uno en uno, así que lo que manda de verdad es
     * upload_max_filesize. Se lo enseñamos al administrador para que sepa
     * cuánto puede pesar cada foto o vídeo sin adivinar.
     */
    protected function uploadLimitsHint(): string
    {
        $upload = ini_get('upload_max_filesize') ?: '?';
        $post = ini_get('post_max_size') ?: '?';

        return "Cada archivo se sube por separado, así que el peso máximo real de cada foto o vídeo "
            . "lo marca tu servidor: upload_max_filesize = {$upload} (post_max_size = {$post}). "
            . 'Para permitir vídeos grandes, sube esos valores en el php.ini y, si usas Nginx, '
            . 'client_max_body_size.';
    }

    protected function socialsTab(): Tab
    {
        return Tab::make('Redes sociales')
            ->icon('ri-share-circle-line')
            ->schema([
                Section::make('Enlaces')
                    ->description('Deja en blanco las redes que no uses: no se mostrarán en la web. El bloque se activa desde la pestaña General.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('social_facebook')->label('Facebook')->url()->prefixIcon('ri-facebook-circle-fill'),
                        TextInput::make('social_discord')->label('Discord')->url()->prefixIcon('ri-discord-fill'),
                        TextInput::make('social_instagram')->label('Instagram')->url()->prefixIcon('ri-instagram-fill'),
                        TextInput::make('social_whatsapp_channel')->label('Canal de WhatsApp')->url()->prefixIcon('ri-whatsapp-fill'),
                        TextInput::make('social_whatsapp_group')->label('Grupo de WhatsApp')->url()->prefixIcon('ri-group-2-fill'),
                        TextInput::make('social_telegram')->label('Telegram')->url()->prefixIcon('ri-telegram-fill'),
                        TextInput::make('social_youtube')->label('YouTube')->url()->prefixIcon('ri-youtube-fill'),
                        TextInput::make('social_tiktok')->label('TikTok')->url()->prefixIcon('ri-tiktok-fill'),
                        TextInput::make('social_x')->label('X / Twitter')->url()->prefixIcon('ri-twitter-x-fill'),
                        TextInput::make('social_github')->label('GitHub')->url()->prefixIcon('ri-github-fill'),
                    ]),
            ]);
    }

    // ------------------------------------------------------------------
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar cambios')
                ->icon('ri-save-3-line')
                ->action('save')
                ->keyBindings(['mod+s']),

            Action::make('activate')
                ->label(Installer::isActive() ? 'Tema activo' : 'Activar tema')
                ->icon('ri-magic-line')
                ->color(Installer::isActive() ? 'gray' : 'success')
                ->disabled(Installer::isActive())
                ->requiresConfirmation()
                ->modalDescription('El tema Cyberpunk pasará a ser el tema activo de la tienda.')
                ->action(function () {
                    $this->authorizeUpdate();

                    if (!Installer::hasAssets()) {
                        Notification::make()
                            ->title('Faltan los estilos del tema')
                            ->body('No existe public/cyberpunk/manifest.json. Copia la carpeta public/ del paquete o ejecuta "npm run build cyberpunk" antes de activarlo, o la tienda se verá sin diseño.')
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }

                    if (Installer::activateTheme()) {
                        Notification::make()->title('Tema Cyberpunk activado')->success()->send();
                    } else {
                        Notification::make()
                            ->title('No se pudo activar')
                            ->body('No se encontró la carpeta themes/cyberpunk. Usa "Reinstalar archivos".')
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('reinstall')
                ->label('Reinstalar archivos')
                ->icon('ri-refresh-line')
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription('Vuelve a copiar el tema y los assets compilados desde la extensión. No borra tu configuración.')
                ->action(function () {
                    $this->authorizeUpdate();

                    $report = Installer::install(overwriteSettings: false, activate: false);

                    if (count($report['errors']) > 0) {
                        Notification::make()
                            ->title('Reinstalación con avisos')
                            ->body(implode(' · ', $report['errors']))
                            ->warning()
                            ->send();

                        return;
                    }

                    Notification::make()->title('Archivos del tema reinstalados')->success()->send();
                }),

            Action::make('repairDatabase')
                ->label('Reparar base de datos')
                ->icon('ri-database-2-line')
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription('Crea las tablas de la comunidad que falten. No borra nada de lo que ya exista.')
                ->action(function () {
                    $this->authorizeUpdate();

                    try {
                        $created = Database::ensureTables();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('No se pudieron crear las tablas')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title(count($created) > 0 ? 'Base de datos reparada' : 'Todo estaba correcto')
                        ->body(count($created) > 0
                            ? 'Tablas creadas: ' . implode(', ', $created)
                            : 'Las tablas de la comunidad ya existían.')
                        ->success()
                        ->send();
                }),

            Action::make('resetVisits')
                ->label('Reiniciar visitas')
                ->icon('ri-eye-off-line')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $this->authorizeUpdate();
                    Visits::reset();
                    Notification::make()->title('Contador de visitas reiniciado')->success()->send();
                }),

            Action::make('resetAll')
                ->label('Restablecer todo')
                ->icon('ri-arrow-go-back-line')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Restablecer la configuración de fábrica')
                ->modalDescription('Se borrarán TODOS los ajustes del tema (colores, banner, marketing, páginas y redes) y se volverá a la configuración original. Las publicaciones de la comunidad no se tocan.')
                ->action(function () {
                    $this->authorizeUpdate();

                    Config::reset();
                    $this->form->fill(Config::all());

                    Notification::make()->title('Configuración restablecida')->success()->send();
                }),
        ];
    }

    public function save(): void
    {
        $this->authorizeUpdate();

        $data = $this->form->getState();

        // Normalizamos las listas para no guardar filas vacías
        foreach (Config::ARRAY_KEYS as $key) {
            if (!isset($data[$key]) || !is_array($data[$key])) {
                $data[$key] = [];
                continue;
            }

            $data[$key] = array_values(array_filter($data[$key], function ($item) {
                if (!is_array($item)) {
                    return $item !== null && $item !== '';
                }

                return count(array_filter($item, fn ($value) => $value !== null && $value !== '' && $value !== false)) > 0;
            }));
        }

        // El slug de la comunidad siempre limpio
        if (isset($data['community_slug'])) {
            $data['community_slug'] = Str::slug((string) $data['community_slug']) ?: 'comunidad';
        }

        if (isset($data['reviews_slug'])) {
            $data['reviews_slug'] = Str::slug((string) $data['reviews_slug']) ?: 'resenas';
        }

        Config::save($data);

        Notification::make()
            ->title('Configuración guardada')
            ->body('Los cambios ya están aplicados en la web.')
            ->success()
            ->send();
    }

    protected function authorizeUpdate(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless($user && ($user->hasPermission('admin.settings.update') || $user->hasPermission('admin.cyberpunk.update')), 403);
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && ($user->hasPermission('admin.settings.view') || $user->hasPermission('admin.cyberpunk.view'));
    }

    public function getSubheading(): ?string
    {
        $state = Installer::isActive() ? 'activo' : 'no activo';
        $assets = Installer::hasAssets() ? 'assets compilados listos' : 'faltan los assets compilados';

        return "Tema {$state} · {$assets}";
    }

    /**
     * Datos auxiliares para la vista.
     */
    public function getViewData(): array
    {
        return [
            'themeActive' => Installer::isActive(),
            'hasAssets' => Installer::hasAssets(),
            'palettes' => Palettes::all(),
            'defaults' => Defaults::settings(),
        ];
    }
}
