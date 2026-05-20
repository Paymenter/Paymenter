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
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConfigOptionResource extends Resource
{
    protected static ?string $model = ConfigOption::class;

    public static function getNavigationLabel(): string
    {
        return __('config_options.config_options');
    }

    public static function getModelLabel(): string
    {
        return __('config_options.config_option_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('config_options.config_options_plural_label');
    }

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
                        Tab::make(__('config_options.general'))->schema([
                            TextInput::make('name')
                                ->label(__('config_options.name'))
                                ->required()
                                ->maxLength(255)
                                ->placeholder(__('config_options.enter_name')),
                            TextInput::make('description')
                                ->label(__('config_options.description'))
                                ->columnSpanFull()
                                ->maxLength(255)
                                ->placeholder(__('config_options.enter_description')),
                            TextInput::make('env_variable')
                                ->label(__('config_options.env_variable'))
                                ->maxLength(255)
                                ->placeholder(__('config_options.enter_env_variable')),
                            Select::make('type')
                                ->label(__('config_options.type'))
                                ->native(false)
                                ->required()
                                ->reactive()
                                ->options([
                                    'text' => __('config_options.types.text'),
                                    'number' => __('config_options.types.number'),
                                    'select' => __('config_options.types.select'),
                                    'radio' => __('config_options.types.radio'),
                                    'checkbox' => __('config_options.types.checkbox'),
                                    'slider' => __('config_options.types.slider'),
                                ]),
                            Checkbox::make('hidden')
                                ->label(__('config_options.hidden')),
                            Checkbox::make('upgradable')
                                ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'slider']))
                                ->label(__('config_options.upgradable'))
                                ->helperText(__('config_options.upgradable_helper')),
                            Select::make('products')
                                ->label(__('config_options.products'))
                                ->relationship('products', 'name')
                                ->multiple()
                                ->preload()
                                ->placeholder(__('config_options.select_products')),
                        ]),
                        Tab::make(__('config_options.options'))
                            ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'slider', 'checkbox']))
                            ->schema([
                                Repeater::make('Options')
                                    ->relationship('children')
                                    ->label(__('config_options.options'))
                                    ->addActionLabel(__('config_options.add_option'))
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
                                            ->label(__('config_options.name'))
                                            ->required()
                                            ->live()
                                            ->maxLength(255)
                                            ->placeholder(__('config_options.enter_option_name')),
                                        TextInput::make('env_variable')
                                            ->label(__('config_options.env_variable'))
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder(__('config_options.enter_option_env_variable')),
                                        // if the type is select, radio or checkbox then allow unlimited children (otherwise only allow 1)
                                        ProductResource::plan()->columnSpanFull()->label(__('config_options.pricing'))->reorderable(false)->deleteAction(null),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('config_options.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('env_variable')
                    ->label(__('config_options.env_variable'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('config_options.type'))
                    ->formatStateUsing(fn (string $state) => __('config_options.types.' . $state) ?? $state)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hidden')
                    ->badge()
                    ->label(__('config_options.hidden'))
                    ->formatStateUsing(fn ($state) => $state ? __('config_options.yes') : __('config_options.no'))
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
