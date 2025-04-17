<?php

namespace App\Admin\Resources;

use App\Admin\Resources\TaxRateResource\Pages;
use App\Models\TaxRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;

class TaxRateResource extends Resource
{
    protected static ?string $model = TaxRate::class;

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $navigationIcon = 'ri-wallet-3-line';

    public static function form(Form $form): Form
    {
        $countries = ['all' => 'All Countries'] + config('app.countries');
        unset($countries['']);

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter the name of the tax rate'),
                Forms\Components\TextInput::make('rate')
                    ->label('Rate')
                    ->mask(RawJs::make(
                        <<<'JS'
                            $money($input, '.', '', 2)
                        JS
                    ))
                    ->required()
                    ->suffix('%')
                    ->placeholder('Enter the rate of the tax rate'),
                Forms\Components\Select::make('country')
                    ->label('Country')
                    ->required()
                    ->unique(null, 'country', ignoreRecord: true)
                    ->options($countries),
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
                Tables\Columns\TextColumn::make('rate')
                    ->label('Rate')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->formatStateUsing(fn (string $state): string => config('app.countries')[$state] ?? 'All Countries')
                    ->label('Country')
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

    public static function canAccess(): bool
    {
        return config('settings.tax_enabled') ? true && static::canViewAny() : false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaxRates::route('/'),
            'create' => Pages\CreateTaxRate::route('/create'),
            'edit' => Pages\EditTaxRate::route('/{record}/edit'),
        ];
    }
}
