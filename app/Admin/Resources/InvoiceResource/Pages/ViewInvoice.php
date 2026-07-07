<?php

namespace App\Admin\Resources\InvoiceResource\Pages;

use App\Admin\Resources\InvoiceResource;
use App\Admin\Resources\InvoiceResource\RelationManagers\AdjustmentNotesRelationManager;
use App\Admin\Resources\InvoiceResource\RelationManagers\TransactionsRelationManager;
use App\Enums\CancellationReason;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use App\Classes\PDF;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if (!config('settings.immutable_invoices_enabled', false)) {
            redirect(InvoiceResource::getUrl('edit', ['record' => $record]));
        }
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invoice Details')
                    ->schema([
                        TextEntry::make('number')
                            ->label('Invoice Number'),
                        TextEntry::make('user.name')
                            ->label('User'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'draft' => 'gray',
                                default => 'danger',
                            })
                            ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                        TextEntry::make('currency.code')
                            ->label('Currency'),
                        TextEntry::make('created_at')
                            ->label('Issued At')
                            ->dateTime(),
                        TextEntry::make('due_at')
                            ->label('Due At')
                            ->date(),
                        TextEntry::make('formattedTotal')
                            ->label('Total'),
                        TextEntry::make('formattedCurrentBalance')
                            ->label('Remaining'),
                    ])
                    ->columns(3),
                Section::make('Items')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('description')
                                    ->label('Description'),
                                TextEntry::make('price')
                                    ->label('Price')
                                    ->money(fn($record) => $record->invoice->currency_code ?? 'USD'),
                                TextEntry::make('quantity')
                                    ->label('Quantity'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cancel')
                ->label('Cancel Invoice')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    Select::make('cancellation_reason')
                        ->label('Cancellation Reason')
                        ->options(CancellationReason::class)
                        ->required(),
                ])
                ->action(function (Invoice $invoice, array $data) {
                    $invoice->update([
                        'status' => Invoice::STATUS_CANCELLED,
                        'cancellation_reason' => $data['cancellation_reason'],
                    ]);
                })
                ->visible(fn(Invoice $invoice): bool => $invoice->status !== Invoice::STATUS_CANCELLED),
            Action::make('pdf')
                ->label('Download PDF')
                ->action(function (Invoice $invoice) {
                    return response()->streamDownload(function () use ($invoice) {
                        echo PDF::generateInvoice($invoice)->stream();
                    }, 'invoice-' . ($invoice->number ?? $invoice->id) . '.pdf');
                }),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            TransactionsRelationManager::class,
            AdjustmentNotesRelationManager::class
        ];
    }
}
