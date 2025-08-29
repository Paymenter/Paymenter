<?php

namespace App\Admin\Resources\InvoiceTransactions\Tables;

use App\Admin\Resources\InvoiceResource;
use App\Models\InvoiceTransaction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_id')
                    ->url(fn (InvoiceTransaction $record): string => InvoiceResource::getUrl('edit', ['record' => $record->invoice_id]))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gateway.name')
                    ->sortable(),
                TextColumn::make('formattedAmount')
                    ->label('Amount')
                    ->sortable(),
                TextColumn::make('formattedFee')
                    ->label('Fee')
                    ->sortable(),
                TextColumn::make('transaction_id')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
