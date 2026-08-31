<?php

namespace App\Admin\Resources\InvoiceResource\Pages;

use App\Admin\Actions\AuditAction;
use App\Admin\Resources\InvoiceResource;
use App\Classes\PDF;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if (config('settings.immutable_invoices_enabled', false)) {
            redirect(InvoiceResource::getUrl('view', ['record' => $record]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->label('Publish')
                ->action(function (Invoice $record) {
                    $this->changeInvoiceStatusToPending($record);
                })
                ->visible(fn(Invoice $record): bool => $record->status === Invoice::STATUS_DRAFT)
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->successRedirectUrl(InvoiceResource::getUrl('index')),
            DeleteAction::make(),
            Action::make('pdf')
                ->label('Download PDF')
                ->action(function (Invoice $invoice) {
                    return response()->streamDownload(function () use ($invoice) {
                        echo PDF::generateInvoice($invoice)->stream();
                    }, 'invoice-' . ($invoice->number ?? $invoice->id) . '.pdf');
                }),
            AuditAction::make()
                ->auditChildren([
                    'items',
                    'transactions',
                ]),
        ];
    }

    protected function changeInvoiceStatusToPending(Invoice $invoice): void
    {
        $invoice->status = Invoice::STATUS_PENDING;
        $invoice->save();
    }
}
