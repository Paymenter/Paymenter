<?php

namespace App\Admin\Resources;

use App\Admin\Resources\TaxRateResource\Pages\CreateTaxRate;
use App\Admin\Resources\TaxRateResource\Pages\EditTaxRate;
use App\Admin\Resources\TaxRateResource\Pages\ListTaxRates;
use App\Models\TaxRate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaxRateResource extends Resource
{
    protected static ?string $model = TaxRate::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-wallet-3-line';

    public static function form(Schema $schema): Schema
    {
        $countries = ['all' => 'All Countries'] + config('app.countries');
        unset($countries['']);

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter the name of the tax rate'),
                TextInput::make('rate')
                    ->label('Rate')
                    ->mask(RawJs::make(
                        <<<'JS'
                            $money($input, '.', '', 2)
                        JS
                    ))
                    ->required()
                    ->suffix('%')
                    ->placeholder('Enter the rate of the tax rate'),
                Select::make('country')
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
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rate')
                    ->label('Rate')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country')
                    ->formatStateUsing(fn (string $state): string => config('app.countries')[$state] ?? 'All Countries')
                    ->label('Country')
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

    public static function canAccess(): bool
    {
        return config('settings.tax_enabled') ? true && static::canViewAny() : false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxRates::route('/'),
            'create' => CreateTaxRate::route('/create'),
            'edit' => EditTaxRate::route('/{record}/edit'),
        ];
    }
}
