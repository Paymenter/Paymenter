<?php

namespace App\Admin\Resources\TldResource\RelationManagers;

use App\Models\Currency;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('currency_code')
                    ->label('Currency')
                    ->options(fn () => Currency::orderBy('code')->pluck('code', 'code'))
                    ->searchable()
                    ->required(),
                TextInput::make('years')->integer()->minValue(1)->default(1)->required()
                    ->helperText('Prices below are the total for this term.'),
                TextInput::make('cost')->numeric()->prefix('Cost'),
                TextInput::make('register')->numeric()->prefix('Register'),
                TextInput::make('renew')->numeric()->prefix('Renew'),
                TextInput::make('transfer')->numeric()->prefix('Transfer'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('currency_code')
            ->columns([
                TextColumn::make('currency_code')->label('Currency'),
                TextColumn::make('years')->label('Years'),
                TextColumn::make('cost')->label('Cost'),
                TextColumn::make('register')->label('Register'),
                TextColumn::make('renew')->label('Renew'),
                TextColumn::make('transfer')->label('Transfer'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
