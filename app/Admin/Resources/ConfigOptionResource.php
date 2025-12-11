<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ConfigOptionResource\Pages\CreateConfigOption;
use App\Admin\Resources\ConfigOptionResource\Pages\EditConfigOption;
use App\Admin\Resources\ConfigOptionResource\Pages\ListConfigOptions;
use App\Models\ConfigOption;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConfigOptionResource extends Resource
{
    protected static ?string $model = ConfigOption::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-equalizer-2-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-equalizer-2-fill';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->schema([
                        Tab::make('General')->schema([
                            TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Enter the name of the configuration option'),
                            TextInput::make('env_variable')
                                ->label('Environment Variable')
                                ->maxLength(255)
                                ->placeholder('Enter the environment variable name'),
                            Select::make('type')
                                ->label('Type')
                                ->native(false)
                                ->required()
                                ->reactive()
                                ->options([
                                    'text' => 'Text',
                                    'number' => 'Number',
                                    'select' => 'Select',
                                    'radio' => 'Radio',
                                    'checkbox' => 'Checkbox',
                                    'slider' => 'Slider',
                                    'dynamic_slider' => 'Dynamic Slider',
                                ]),
                            Checkbox::make('hidden')
                                ->label('Hidden'),
                            Checkbox::make('upgradable')
                                ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'slider', 'dynamic_slider']))
                                ->label('Upgradable')
                                ->helperText('If enabled, this configuration option can be upgraded in the future.'),
                            Select::make('products')
                                ->label('Products')
                                ->relationship('products', 'name')
                                ->multiple()
                                ->preload()
                                ->placeholder('Select the products that this configuration option belongs to'),
                        ]),
                        Tab::make('Options')
                            ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'slider', 'checkbox']))
                            ->schema([
                                Repeater::make('Options')
                                    ->relationship('children')
                                    ->label('Options')
                                    ->addActionLabel('Add Option')
                                    ->columnSpanFull()
                                    ->itemLabel(fn (array $state) => $state['name'])
                                    ->collapsible()
                                    ->collapsed()
                                    ->cloneable()
                                    ->reorderable()
                                    ->orderColumn('sort')
                                    ->columns(2)
                                    // When the type is checkbox only allow 1 child
                                    ->maxItems(function (Get $get): ?int {
                                        if (in_array($get('type'), ['select', 'radio', 'slider'])) {
                                            return null; // unlimited children
                                        }

                                        return 1; // checkbox
                                    })
                                    ->minItems(1)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Name')
                                            ->required()
                                            ->live()
                                            ->maxLength(255)
                                            ->placeholder('Enter the name of the configuration option'),
                                        TextInput::make('env_variable')
                                            ->label('Environment Variable')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Enter the environment variable name'),
                                        // if the type is select, radio or checkbox then allow unlimited children (otherwise only allow 1)
                                        ProductResource::plan()->columnSpanFull()->label('Pricing')->reorderable(false)->deleteAction(null),
                                    ]),
                            ]),
                        Tab::make('Dynamic Slider Settings')
                            ->visible(fn (Get $get): bool => $get('type') === 'dynamic_slider')
                            ->schema([
                                Select::make('metadata.resource_type')
                                    ->label('Resource Type')
                                    ->options([
                                        'memory' => 'Memory (MB/GB)',
                                        'cpu' => 'CPU (% / Cores)',
                                        'disk' => 'Disk (MB/GB)',
                                        'custom' => 'Custom',
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        // Auto-populate defaults based on resource type
                                        match ($state) {
                                            'memory' => (function () use ($set) {
                                                $set('metadata.min', 1024);
                                                $set('metadata.max', 65536);
                                                $set('metadata.step', 1024);
                                                $set('metadata.default', 4096);
                                                $set('metadata.unit', 'MB');
                                                $set('metadata.display_unit', 'GB');
                                                $set('metadata.display_divisor', 1024);
                                            })(),
                                            'cpu' => (function () use ($set) {
                                                $set('metadata.min', 100);
                                                $set('metadata.max', 800);
                                                $set('metadata.step', 100);
                                                $set('metadata.default', 100);
                                                $set('metadata.unit', '%');
                                                $set('metadata.display_unit', 'cores');
                                                $set('metadata.display_divisor', 100);
                                            })(),
                                            'disk' => (function () use ($set) {
                                                $set('metadata.min', 10240);
                                                $set('metadata.max', 102400);
                                                $set('metadata.step', 1024);
                                                $set('metadata.default', 20480);
                                                $set('metadata.unit', 'MB');
                                                $set('metadata.display_unit', 'GB');
                                                $set('metadata.display_divisor', 1024);
                                            })(),
                                            default => null,
                                        };
                                    }),
                                Section::make('Slider Range')->columns(4)->schema([
                                    TextInput::make('metadata.min')
                                        ->label('Minimum Value')
                                        ->numeric()
                                        ->required()
                                        ->default(1024),
                                    TextInput::make('metadata.max')
                                        ->label('Maximum Value')
                                        ->numeric()
                                        ->required()
                                        ->default(65536),
                                    TextInput::make('metadata.step')
                                        ->label('Step')
                                        ->numeric()
                                        ->required()
                                        ->default(1024),
                                    TextInput::make('metadata.default')
                                        ->label('Default Value')
                                        ->numeric()
                                        ->required()
                                        ->default(4096),
                                ]),
                                Section::make('Display Units')->columns(3)->schema([
                                    TextInput::make('metadata.unit')
                                        ->label('Storage Unit')
                                        ->required()
                                        ->default('MB')
                                        ->helperText('Unit for internal storage'),
                                    TextInput::make('metadata.display_unit')
                                        ->label('Display Unit')
                                        ->required()
                                        ->default('GB')
                                        ->helperText('Unit shown to customers'),
                                    TextInput::make('metadata.display_divisor')
                                        ->label('Display Divisor')
                                        ->numeric()
                                        ->required()
                                        ->default(1024)
                                        ->helperText('Divide stored value by this for display'),
                                ]),
                                Section::make('Pricing')->schema([
                                    Select::make('metadata.pricing.model')
                                        ->label('Pricing Model')
                                        ->options([
                                            'linear' => 'Linear (base + rate × value)',
                                            'tiered' => 'Tiered (volume discounts)',
                                            'base_addon' => 'Base + Addon (included + overage)',
                                        ])
                                        ->default('linear')
                                        ->required()
                                        ->reactive()
                                        ->columnSpanFull(),
                                    TextInput::make('metadata.pricing.base_price')
                                        ->label('Base Price')
                                        ->numeric()
                                        ->default(0)
                                        ->prefix('$')
                                        ->helperText('Fixed base price added to the calculation'),
                                    // Linear pricing fields
                                    TextInput::make('metadata.pricing.rate_per_unit')
                                        ->label('Rate per Display Unit')
                                        ->numeric()
                                        ->default(2.00)
                                        ->prefix('$')
                                        ->helperText('Price per GB/core/etc. per billing period')
                                        ->visible(fn (Get $get): bool => ($get('metadata.pricing.model') ?? 'linear') === 'linear'),
                                    // Tiered pricing fields
                                    Repeater::make('metadata.pricing.tiers')
                                        ->label('Pricing Tiers')
                                        ->visible(fn (Get $get): bool => $get('metadata.pricing.model') === 'tiered')
                                        ->columnSpanFull()
                                        ->columns(2)
                                        ->addActionLabel('Add Tier')
                                        ->defaultItems(3)
                                        ->schema([
                                            TextInput::make('up_to')
                                                ->label('Up To (units)')
                                                ->numeric()
                                                ->placeholder('∞ (unlimited)')
                                                ->helperText('Leave empty for unlimited'),
                                            TextInput::make('rate')
                                                ->label('Rate per Unit')
                                                ->numeric()
                                                ->required()
                                                ->prefix('$'),
                                        ])
                                        ->helperText('Example: 0-4GB at $3/GB, 4-16GB at $2.50/GB, 16GB+ at $2/GB'),
                                    // Base+Addon pricing fields
                                    TextInput::make('metadata.pricing.included_units')
                                        ->label('Included Units')
                                        ->numeric()
                                        ->default(0)
                                        ->helperText('Units included in base price (e.g., 4 GB)')
                                        ->visible(fn (Get $get): bool => $get('metadata.pricing.model') === 'base_addon'),
                                    TextInput::make('metadata.pricing.overage_rate')
                                        ->label('Overage Rate')
                                        ->numeric()
                                        ->default(2.50)
                                        ->prefix('$')
                                        ->helperText('Price per unit above included amount')
                                        ->visible(fn (Get $get): bool => $get('metadata.pricing.model') === 'base_addon'),
                                ])->columns(2),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('env_variable')
                    ->label('Environment Variable')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hidden')
                    ->badge()
                    ->label('Hidden')
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('parent_id', null))
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('sort', 'asc');
            })
            ->reorderable('sort');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConfigOptions::route('/'),
            'create' => CreateConfigOption::route('/create'),
            'edit' => EditConfigOption::route('/{record}/edit'),
        ];
    }
}
