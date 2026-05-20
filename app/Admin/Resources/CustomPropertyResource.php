<?php

namespace App\Admin\Resources;

use App\Admin\Resources\CustomPropertyResource\Pages\CreateCustomProperty;
use App\Admin\Resources\CustomPropertyResource\Pages\EditCustomProperty;
use App\Admin\Resources\CustomPropertyResource\Pages\ListCustomProperty;
use App\Models\CustomProperty;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get as FormGet;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class CustomPropertyResource extends Resource
{
    protected static ?string $model = CustomProperty::class;

    public static function getNavigationLabel(): string
    {
        return __('custom_properties.custom_properties');
    }

    public static function getModelLabel(): string
    {
        return __('custom_properties.custom_property_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom_properties.custom_properties_plural_label');
    }

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-list-settings-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-list-settings-fill';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('custom_properties.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('key')
                    ->label(__('custom_properties.key'))
                    ->required(),
                Select::make('model')
                    ->label(__('custom_properties.model'))
                    ->options([
                        'App\Models\User' => __('custom_properties.models.App\Models\User'),
                    ])->required(),
                Select::make('type')
                    ->label(__('custom_properties.type'))
                    ->options([
                        'string' => __('custom_properties.types.string'),
                        'text' => __('custom_properties.types.text'),
                        'number' => __('custom_properties.types.number'),
                        'select' => __('custom_properties.types.select'),
                        'checkbox' => __('custom_properties.types.checkbox'),
                        'radio' => __('custom_properties.types.radio'),
                        'date' => __('custom_properties.types.date'),
                    ])
                    ->required()
                    ->live(),
                Textarea::make('description')
                    ->label(__('custom_properties.description'))
                    ->nullable()
                    ->columnSpanFull()
                    ->rows(2),
                TagsInput::make('allowed_values')
                    ->label(__('custom_properties.allowed_values'))
                    ->visible(fn (FormGet $get) => in_array($get('type'), ['select', 'radio']))
                    ->nullable(),
                TextInput::make('validation')
                    ->label(__('custom_properties.validation'))
                    ->nullable(),

                Section::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        Toggle::make('non_editable')
                            ->label(__('custom_properties.non_editable'))
                            ->default(false),
                        Toggle::make('required')
                            ->label(__('custom_properties.required'))
                            ->default(false),
                        Toggle::make('show_on_invoice')
                            ->label(__('custom_properties.show_on_invoice'))
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('custom_properties.property'))
                    ->description(fn (CustomProperty $record): string => mb_strimwidth(($record->description ?? ''),
                        0,
                        75,
                        '...'
                    )),
                TextColumn::make('key')
                    ->label(__('custom_properties.key')),
                TextColumn::make('type')
                    ->label(__('custom_properties.type'))
                    ->formatStateUsing(fn ($state) => __('custom_properties.types.' . $state) ?? str($state)->title()),
                ToggleColumn::make('non_editable')
                    ->label(__('custom_properties.non_editable'))
                    ->disabled(fn (): bool => Gate::denies('has-permission', 'admin.custom_properties.update')),
                ToggleColumn::make('required')
                    ->label(__('custom_properties.required'))
                    ->disabled(fn (): bool => Gate::denies('has-permission', 'admin.custom_properties.update')),
                ToggleColumn::make('show_on_invoice')
                    ->label(__('custom_properties.show_on_invoice'))
                    ->disabled(fn (): bool => Gate::denies('has-permission', 'admin.custom_properties.update')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])->defaultGroup('model');
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
            'index' => ListCustomProperty::route('/'),
            'create' => CreateCustomProperty::route('/create'),
            'edit' => EditCustomProperty::route('/{record}/edit'),
        ];
    }
}
