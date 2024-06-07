<?php

namespace App\Admin\Resources;

use App\Admin\Resources\CurrencyResource\Pages;
use App\Admin\Resources\CurrencyResource\RelationManagers;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Code')
                    ->required()
                    ->maxLength(3)
                    ->disabledOn('edit')
                    ->unique(static::getModel(), 'code', ignoreRecord: true)
                    ->placeholder('Enter the currency code'),
                Forms\Components\TextInput::make('prefix')
                    ->label('Prefix')
                    ->maxLength(10)
                    ->placeholder('Enter the currency prefix'),
                Forms\Components\TextInput::make('suffix')
                    ->label('Suffix')
                    ->maxLength(10)
                    ->placeholder('Enter the currency suffix'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prefix')
                    ->label('Prefix')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('suffix')
                    ->label('Suffix')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
