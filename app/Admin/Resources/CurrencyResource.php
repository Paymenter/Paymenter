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

    public static function getNavigationLabel(): string
    {
        return __('currencies.currencies');
    }

    public static function getModelLabel(): string
    {
        return __('currencies.currency_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('currencies.currencies_plural_label');
    }

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-money-dollar-circle-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-money-dollar-circle-fill';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label(__('currencies.code'))
                    ->required()
                    ->maxLength(3)
                    ->disabledOn('edit')
                    ->unique(static::getModel(), 'code', ignoreRecord: true)
                    ->placeholder(__('currencies.enter_code')),
                TextInput::make('name')
                    ->label(__('currencies.name'))
                    ->helperText(__('currencies.name_helper'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('currencies.enter_name')),
                TextInput::make('prefix')
                    ->label(__('currencies.prefix'))
                    ->maxLength(10)
                    ->placeholder(__('currencies.enter_prefix')),
                TextInput::make('suffix')
                    ->label(__('currencies.suffix'))
                    ->maxLength(10)
                    ->placeholder(__('currencies.enter_suffix')),
                Select::make('format')
                    ->label(__('currencies.format'))
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
                    ->label(__('currencies.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('prefix')
                    ->label(__('currencies.prefix'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('suffix')
                    ->label(__('currencies.suffix'))
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
