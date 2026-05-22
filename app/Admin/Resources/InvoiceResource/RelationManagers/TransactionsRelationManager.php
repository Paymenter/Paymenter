<?php

namespace App\Admin\Resources\InvoiceResource\RelationManagers;

use App\Models\Invoice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected function canModifyTransactions(): bool
    {
        return $this->getOwnerRecord()?->status === Invoice::STATUS_PENDING;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('gateway.name')
                    ->label('Gateway')
                    ->relationship('gateway', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select the gateway'),
                TextInput::make('transaction_id')
                    ->label('Transaction ID'),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->mask(RawJs::make(
                        <<<'JS'
                            $money($input, '.', '', 2)
                        JS
                    ))
                    ->required(),
                TextInput::make('fee')
                    ->numeric()
                    ->mask(RawJs::make(
                        <<<'JS'
                            $money($input, '.', '', 2)
                        JS
                    ))
                    ->label('Fee'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('transaction_id')
            ->columns([
                TextColumn::make('gateway.name')->label('Gateway'),
                TextColumn::make('transaction_id'),
                TextColumn::make('formattedAmount')->label('Amount'),
                TextColumn::make('formattedFee')->label('Fee'),
                TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn (): bool => $this->canModifyTransactions()),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->visible(fn (): bool => $this->canModifyTransactions()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }


    public function isReadOnly(): bool {
        return false;
    }
}
