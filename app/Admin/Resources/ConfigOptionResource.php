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
                                ]),
                            Checkbox::make('hidden')
                                ->label('Hidden'),
                            Checkbox::make('upgradable')
                                ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'slider']))
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
