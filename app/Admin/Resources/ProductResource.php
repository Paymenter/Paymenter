<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ProductResource\Pages\CreateProduct;
use App\Admin\Resources\ProductResource\Pages\EditProduct;
use App\Admin\Resources\ProductResource\Pages\ListProducts;
use App\Classes\FilamentInput;
use App\Helpers\ExtensionHelper;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Server;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function getNavigationLabel(): string
    {
        return __('products.products');
    }

    public static function getModelLabel(): string
    {
        return __('products.product_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('products.products_plural_label');
    }

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-instance-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-instance-fill';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make(__('products.general'))
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('products.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old)) {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),
                                TextInput::make('slug')
                                    ->label(__('products.slug'))
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('stock')
                                    ->label(__('products.stock'))
                                    ->integer()
                                    ->nullable(),
                                TextInput::make('per_user_limit')
                                    ->label(__('products.per_user_limit'))
                                    ->integer()
                                    ->nullable(),
                                Select::make('allow_quantity')
                                    ->label(__('products.allow_quantity'))
                                    ->options([
                                        'disabled' => __('products.no'),
                                        'separated' => __('products.separated'),
                                        'combined' => __('products.combined'),
                                    ])->default('separated')
                                    ->required(),
                                Textarea::make('email_template')
                                    ->label(__('products.email_template'))
                                    ->hint(__('products.email_template_hint'))
                                    ->nullable(),
                                Checkbox::make('hidden')
                                    ->label(__('products.hide_product'))
                                    ->hint(__('products.hide_product_hint')),

                                RichEditor::make('description')
                                    ->label(__('products.description'))
                                    ->nullable()
                                    ->columnSpanFull(),
                                FileUpload::make('image')
                                    ->label(__('products.image'))
                                    ->nullable()
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->image()
                                    ->disk('public')
                                    ->acceptedFileTypes(['image/*']),
                                Select::make('category_id')
                                    ->label(__('products.category'))
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema) => CategoryResource::form($schema))
                                    ->required(),
                            ]),
                        Tab::make(__('products.pricing'))
                            ->schema([self::plan()]),

                        Tab::make(__('products.upgrades'))
                            ->schema([
                                // Select input for the products this product can upgrade to (hasmany relationship)
                                Select::make('upgrades')
                                    ->label(__('products.upgrades'))
                                    ->relationship('upgrades', 'name', ignoreRecord: true)
                                    ->multiple()
                                    ->preload()
                                    ->placeholder(__('products.upgrades_placeholder')),
                            ]),

                        Tab::make(__('products.server'))
                            ->schema([
                                Select::make('server_id')
                                    ->label(__('products.server'))
                                    ->relationship('server', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->hintAction(
                                        Action::make('refresh')
                                            ->label(__('products.refresh'))
                                            ->action(fn () => Cache::set('product_config', null, 0))
                                            ->hidden(fn (Get $get) => $get('server_id') === null)
                                    )
                                    ->live()
                                    ->afterStateUpdated(fn (Select $component) => $component
                                        ->getContainer()
                                        ->getComponent('extension_settings', withHidden: true)
                                        ->getChildSchema()
                                        ->fill()),

                                Grid::make()
                                    ->hidden(fn (Get $get) => $get('server_id') === null)
                                    ->columns(2)
                                    ->key('extension_settings')
                                    ->schema(
                                        function (Get $get, Component $livewire) {
                                            $server = $get('server_id');
                                            if ($server == null) {
                                                return [];
                                            }
                                            $settings = [];

                                            try {
                                                foreach (ExtensionHelper::getProductConfigOnce(Server::findOrFail($server), $get('settings')) as $setting) {
                                                    // Easier to use dot notation for settings
                                                    $setting['name'] = 'settings.' . $setting['name'];
                                                    $settings[] = FilamentInput::convert($setting);
                                                }
                                            } catch (Exception $e) {
                                                $settings[] = TextEntry::make('error')->state($e->getMessage());
                                            }

                                            return $settings;
                                        }
                                    ),

                            ]),
                    ]),
            ])->columns(1);
    }

    public static function plan()
    {
        return Repeater::make('plan')
            ->label(__('products.plans'))
            ->addActionLabel(__('products.add_new_plan'))
            ->relationship('plans')
            ->name('name')
            ->reorderable()
            ->cloneable()
            ->collapsible()
            ->collapsed()
            ->orderColumn()
            ->defaultItems(1)
            ->minItems(1)
            ->columns(2)
            ->deleteAction(function (Action $action) {
                $action->before(function (?Product $record, $state, Action $action, array $arguments) {
                    if (!$record) {
                        return;
                    }
                    $key = $arguments['item'];
                    if (!isset($state[$key]['id'])) {
                        return;
                    }
                    $plan = $record->plans()->find($state[$key]['id']);
                    if ($plan->services()->count() > 0) {
                        Notification::make()
                            ->title(__('products.plan_delete_error_title'))
                            ->body(__('products.plan_delete_error_body'))
                            ->danger()
                            ->send();
                        $action->cancel();
                    }
                });
            })
            ->itemLabel(fn (array $state) => $state['name'])
            ->schema([
                TextInput::make('name')
                    ->label(__('products.name'))
                    ->required()
                    ->live(onBlur: true)
                    ->maxLength(255),
                Select::make('type')
                    ->label(__('products.type'))
                    ->options([
                        'free' => __('products.free'),
                        'one-time' => __('products.one_time'),
                        'recurring' => __('products.recurring'),
                    ])
                    ->required()
                    ->live(debounce: 300)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if ($state === 'free') {
                            $set('every', null);
                            $set('price', 0);
                        }
                    })
                    ->placeholder(__('products.select_type_placeholder'))
                    ->default('free'),

                TextInput::make('billing_period')
                    ->required()
                    ->label(__('products.time_interval'))
                    ->default(1)
                    ->hidden(fn (Get $get) => $get('type') !== 'recurring'),

                Select::make('billing_unit')
                    ->options([
                        'day' => __('products.day'),
                        'week' => __('products.week'),
                        'month' => __('products.month'),
                        'year' => __('products.year'),
                    ])
                    ->label(__('products.billing_unit'))
                    ->required()
                    ->default('month')
                    ->hidden(fn (Get $get) => $get('type') !== 'recurring'),
                Repeater::make('pricing')
                    ->hidden(fn (Get $get) => $get('type') === 'free')
                    ->columns(3)
                    ->addActionLabel(__('products.add_new_price'))
                    ->reorderable(false)
                    ->relationship('prices')
                    ->columnSpanFull()
                    ->maxItems(Currency::count())
                    ->defaultItems(1)
                    ->itemLabel(fn (array $state) => $state['currency_code'])
                    ->schema([
                        Select::make('currency_code')
                            ->label(__('products.currency'))
                            ->options(function (Get $get, ?string $state) {
                                $pricing = collect($get('../../pricing'))->pluck('currency_code');
                                if ($state !== null) {
                                    $pricing = $pricing->filter(function ($code) use ($state) {
                                        return $code !== $state;
                                    });
                                }
                                $pricing = $pricing->filter(function ($code) {
                                    return $code !== null;
                                });

                                return Currency::whereNotIn('code', $pricing)->pluck('code', 'code');
                            })
                            ->live()
                            ->default(config('settings.default_currency'))
                            ->required(),
                        TextInput::make('price')
                            ->required()
                            ->label(__('products.price'))
                            // Suffix based on chosen currency
                            ->prefix(fn (Get $get) => Currency::where('code', $get('currency_code'))->first()?->prefix)
                            ->suffix(fn (Get $get) => Currency::where('code', $get('currency_code'))->first()?->suffix)
                            ->live(onBlur: true)
                            ->mask(RawJs::make(
                                <<<'JS'
                                    $money($input, '.', '', 2)
                                JS
                            ))
                            ->numeric()
                            ->minValue(0)
                            ->hidden(fn (Get $get) => $get('type') === 'free'),
                        TextInput::make('setup_fee')
                            ->label(__('products.setup_fee'))
                            ->live(onBlur: true)
                            ->mask(RawJs::make(
                                <<<'JS'
                                    $money($input, '.', '', 2)
                                JS
                            ))
                            ->numeric()
                            ->minValue(0)
                            ->hidden(fn (Get $get) => $get('type') === 'free'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('products.name'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('products.name', 'like', "%{$search}%");
                    }),
                TextColumn::make('slug')
                    ->label(__('products.slug')),
                TextColumn::make('category.name')
                    ->label(__('products.category'))
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label(__('products.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('sort', 'asc');
            })
            ->defaultGroup('category.name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
