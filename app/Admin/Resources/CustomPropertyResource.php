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

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-list-settings-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-list-settings-fill';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('key')->required(),
                Select::make('model')->options([
                    'App\Models\User' => 'User',
                ])->required(),
                Select::make('type')->options([
                    'string' => 'Short Text',
                    'text' => 'Long Text',
                    'number' => 'Number',
                    'select' => 'Select',
                    'checkbox' => 'Checkbox',
                    'radio' => 'Radio',
                    'date' => 'Date',
                ])
                    ->required()
                    ->live(),
                Textarea::make('description')->nullable()->columnSpanFull()->rows(2),
                TagsInput::make('allowed_values')
                    ->visible(fn (FormGet $get) => in_array($get('type'), ['select', 'radio']))
                    ->nullable(),
                TextInput::make('validation')->nullable(),

                Section::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        Toggle::make('non_editable')->default(false),
                        Toggle::make('required')->default(false),
                        Toggle::make('show_on_invoice')->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Property')
                    ->description(fn (CustomProperty $record): string => mb_strimwidth(($record->description ?? ''),
                        0,
                        75,
                        '...'
                    )),
                TextColumn::make('key'),
                TextColumn::make('type')->formatStateUsing(fn ($state) => str($state)->title()),
                ToggleColumn::make('non_editable')
                    ->disabled(fn (): bool => Gate::denies('has-permission', 'admin.custom_properties.update')),
                ToggleColumn::make('required')
                    ->disabled(fn (): bool => Gate::denies('has-permission', 'admin.custom_properties.update')),
                ToggleColumn::make('show_on_invoice')
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
