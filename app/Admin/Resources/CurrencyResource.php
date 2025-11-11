<?php

namespace App\Admin\Resources;

use App\Admin\Resources\CurrencyResource\Pages\CreateCurrency;
use App\Admin\Resources\CurrencyResource\Pages\EditCurrency;
use App\Admin\Resources\CurrencyResource\Pages\ListCurrencies;
use App\Models\Currency;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-money-dollar-circle-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-money-dollar-circle-fill';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Code')
                    ->required()
                    ->maxLength(3)
                    ->disabledOn('edit')
                    ->unique(static::getModel(), 'code', ignoreRecord: true)
                    ->placeholder('Enter the currency code'),
                TextInput::make('name')
                    ->label('Name')
                    ->helperText('Display name for customers, e.g., US Dollar')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter the currency name'),
                TextInput::make('prefix')
                    ->label('Prefix')
                    ->maxLength(10)
                    ->placeholder('Enter the currency prefix'),
                TextInput::make('suffix')
                    ->label('Suffix')
                    ->maxLength(10)
                    ->placeholder('Enter the currency suffix'),
                Select::make('format')
                    ->label('Format')
                    ->options([
                        '1.000,00' => '1.000,00',
                        '1,000.00' => '1,000.00',
                        '1 000,00' => '1 000,00',
                        '1 000.00' => '1 000.00',
                    ])
                    ->default('1.000,00'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('prefix')
                    ->label('Prefix')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('suffix')
                    ->label('Suffix')
                    ->searchable()
                    ->sortable(),
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
            ]);
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
            'index' => ListCurrencies::route('/'),
            'create' => CreateCurrency::route('/create'),
            'edit' => EditCurrency::route('/{record}/edit'),
        ];
    }
}
