<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\UserResource;
use App\Models\Currency;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class ShowCredits extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'credits';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-coin-line';

    public static function getNavigationLabel(): string
    {
        return __('users.credits');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('currency_code')
                    ->label(__('orders.currency'))
                    ->options(function () {
                        $existing_currencies = $this->getOwnerRecord()->credits->pluck('currency_code');

                        return Currency::whereNotIn('code', $existing_currencies)->pluck('code', 'code');
                    })
                    ->live()
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->label(__('users.amount'))
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
                TextColumn::make('currency.code')
                    ->label(__('orders.currency')),
                TextColumn::make('formattedAmount')->label(__('users.formatted_amount')),
                TextInputColumn::make('amount')->label(__('users.amount')),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()->disabled(function () {
                    $existing_currencies = $this->getOwnerRecord()->credits->pluck('currency_code');

                    return count(Currency::whereNotIn('code', $existing_currencies)->pluck('code', 'code')) <= 0;
                }),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }
}
