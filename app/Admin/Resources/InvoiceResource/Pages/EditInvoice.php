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

    protected function getHeaderActions(): array
    {
        return [
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
}
