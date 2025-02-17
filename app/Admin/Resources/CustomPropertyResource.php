<?php

namespace App\Admin\Resources;

use App\Admin\Resources\CustomPropertyResource\Pages;
use App\Models\CustomProperty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomPropertyResource extends Resource
{
    protected static ?string $model = CustomProperty::class;

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $navigationIcon = 'ri-list-settings-line';

    protected static ?string $activeNavigationIcon = 'ri-list-settings-fill';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('key')->required(),
                Forms\Components\Select::make('model')->options([
                    'App\Models\User' => 'User',
                ])->required(),
                Forms\Components\Select::make('type')->options([
                    'string' => 'Short Text',
                    'text' => 'Long Text',
                    'number' => 'Number',
                    'select' => 'Select',
                    'checkbox' => 'Checkbox',
                    'radio' => 'Radio',
                    'date' => 'Date',
                ])->required(),
                Forms\Components\Textarea::make('description')->nullable()->columnSpanFull()->rows(2),
                Forms\Components\TagsInput::make('allowed_values')->nullable(),
                Forms\Components\TextInput::make('validation')->nullable(),

                Forms\Components\Section::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        Forms\Components\Toggle::make('non_editable')->default(false),
                        Forms\Components\Toggle::make('required')->default(false),
                        Forms\Components\Toggle::make('show_on_invoice')->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Property')
                    ->description(fn (CustomProperty $record): string => mb_strimwidth(($record->description ?? ''),
                        0,
                        75,
                        '...'
                    )),
                Tables\Columns\TextColumn::make('key'),
                Tables\Columns\TextColumn::make('type')->formatStateUsing(fn ($state) => str($state)->title()),
                Tables\Columns\ToggleColumn::make('non_editable'),
                Tables\Columns\ToggleColumn::make('required'),
                Tables\Columns\ToggleColumn::make('show_on_invoice'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCustomProperty::route('/'),
            'create' => Pages\CreateCustomProperty::route('/create'),
            'edit' => Pages\EditCustomProperty::route('/{record}/edit'),
        ];
    }
}
