<?php

namespace App\Admin\Resources\InvoiceTransactions\Tables;

use App\Admin\Resources\InvoiceResource;
use App\Enums\InvoiceTransactionStatus;
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
                    ->label(__('invoices.invoice_id'))
                    ->url(fn (InvoiceTransaction $record): string => InvoiceResource::getUrl('edit', ['record' => $record->invoice_id]))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gateway.name')
                    ->label(__('invoices.gateway'))
                    ->sortable(),
                TextColumn::make('formattedAmount')
                    ->label(__('invoices.amount'))
                    ->sortable(),
                TextColumn::make('formattedFee')
                    ->label(__('invoices.fee'))
                    ->sortable(),
                TextColumn::make('transaction_id')
                    ->label(__('invoices.transaction_id'))
                    ->searchable(),
                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn (InvoiceTransaction $record) => match ($record->status) {
                        InvoiceTransactionStatus::Succeeded => 'success',
                        InvoiceTransactionStatus::Processing => 'warning',
                        InvoiceTransactionStatus::Failed => 'danger',
                        default => null,
                    })
                    ->formatStateUsing(fn (InvoiceTransactionStatus $state): string => __('invoices.transaction_statuses.' . $state->value))
                    ->label(__('invoices.status')),
                TextColumn::make('created_at')
                    ->label(__('invoices.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('invoices.updated_at'))
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
