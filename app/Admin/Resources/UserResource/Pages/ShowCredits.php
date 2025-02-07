<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\UserResource;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;

class ShowCredits extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'credits';

    protected static ?string $navigationIcon = 'ri-bill-line';

    public static function getNavigationLabel(): string
    {
        return 'Credits';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('currency_code')
                    ->options(function () {
                        $existing_currencies = $this->getOwnerRecord()->credits->pluck('currency_code');

                        return Currency::whereNotIn('code', $existing_currencies)->pluck('code', 'code');
                    })
                    ->live()
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->label('Amount')
                    // Suffix based on chosen currency
                    ->prefix(fn (Get $get) => Currency::where('code', $get('currency_code'))->first()?->prefix)
                    ->suffix(fn (Get $get) => Currency::where('code', $get('currency_code'))->first()?->suffix)
                    ->live(onBlur: true)
                    ->mask(RawJs::make(
                        <<<'JS'
                        $money($input, '.', '', 2)
                        JS
                    ))
                    ->numeric()
                    ->minValue(0),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('currency.code')
            ->columns([
                Tables\Columns\TextColumn::make('currency.code'),
                Tables\Columns\TextColumn::make('formattedAmount')->label('Formatted Amount'),
                Tables\Columns\TextInputColumn::make('amount')->label('Amount'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->disabled(function () {
                    $existing_currencies = $this->getOwnerRecord()->credits->pluck('currency_code');

                    return count(Currency::whereNotIn('code', $existing_currencies)->pluck('code', 'code')) <= 0;
                }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
