<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ConfigOptionResource\Pages;
use App\Models\ConfigOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConfigOptionResource extends Resource
{
    protected static ?string $model = ConfigOption::class;

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $navigationIcon = 'ri-equalizer-2-line';

    protected static ?string $activeNavigationIcon = 'ri-equalizer-2-fill';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Tabs\Tab::make('General')->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Enter the name of the configuration option'),
                            Forms\Components\TextInput::make('env_variable')
                                ->label('Environment Variable')
                                ->maxLength(255)
                                ->placeholder('Enter the environment variable name'),
                            Forms\Components\Select::make('type')
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
                            Forms\Components\Checkbox::make('hidden')
                                ->label('Hidden'),
                            Forms\Components\Checkbox::make('upgradable')
                                ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'slider']))
                                ->label('Upgradable')
                                ->helperText('If enabled, this configuration option can be upgraded in the future.'),
                            Forms\Components\Select::make('products')
                                ->label('Products')
                                ->relationship('products', 'name')
                                ->multiple()
                                ->preload()
                                ->placeholder('Select the products that this configuration option belongs to'),
                        ]),
                        Forms\Components\Tabs\Tab::make('Options')
                            ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'slider', 'checkbox']))
                            ->schema([
                                Forms\Components\Repeater::make('Options')
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
                                        Forms\Components\TextInput::make('name')
                                            ->label('Name')
                                            ->required()
                                            ->live()
                                            ->maxLength(255)
                                            ->placeholder('Enter the name of the configuration option'),
                                        Forms\Components\TextInput::make('env_variable')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('env_variable')
                    ->label('Environment Variable')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hidden')
                    ->badge()
                    ->label('Hidden')
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('parent_id', null))
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListConfigOptions::route('/'),
            'create' => Pages\CreateConfigOption::route('/create'),
            'edit' => Pages\EditConfigOption::route('/{record}/edit'),
        ];
    }
}
