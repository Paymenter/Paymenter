<?php

namespace App\Admin\Resources\InvoiceResource\RelationManagers;

use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected function canModifyTransactions(): bool
    {
        return !config('settings.immutable_invoices_enabled') || $this->getOwnerRecord()?->status === Invoice::STATUS_PENDING;
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
                TextColumn::make('formattedRefundedAmount')->label(__('invoices.refunded_amount')),
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
                Action::make('refund')
                    ->label(__('invoices.refund'))
                    ->icon('heroicon-o-backward')
                    ->color('warning')
                    ->modalHeading(fn (InvoiceTransaction $record): string => __('invoices.refund_transaction', ['id' => $record->transaction_id ?? $record->id]))
                    ->modalDescription(fn (InvoiceTransaction $record): string => __('invoices.refundable_amount', ['amount' => $record->refundable_amount]))
                    ->form([
                        TextInput::make('amount')
                            ->label(__('invoices.amount'))
                            ->numeric()
                            ->mask(RawJs::make(
                                <<<'JS'
                                    $money($input, '.', '', 2)
                                JS
                            ))
                            ->required()
                            ->rules([
                                fn (InvoiceTransaction $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($record) {
                                    if ((float) $value <= 0) {
                                        $fail(__('invoices.refund_amount_positive'));
                                    }
                                    if ((float) $value > $record->refundable_amount) {
                                        $fail(__('invoices.refund_amount_exceeds_refundable', ['max' => $record->refundable_amount]));
                                    }
                                },
                            ]),
                    ])
                    ->action(function (InvoiceTransaction $record, array $data, Action $action): void {
                        try {
                            ExtensionHelper::refund($record, (float) $data['amount']);

                            Notification::make()
                                ->title(__('invoices.refund_success'))
                                ->success()
                                ->send();

                            $action->success();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title(__('invoices.refund_failed'))
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    })
                    ->visible(fn (InvoiceTransaction $record): bool =>
                        !empty($record->transaction_id) &&
                        $record->refundable_amount > 0 &&
                        Auth::user()->can('update', $record)
                    )
                    ->modalSubmitAction(fn (Action $action) => $action->label(__('invoices.refund'))),
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
