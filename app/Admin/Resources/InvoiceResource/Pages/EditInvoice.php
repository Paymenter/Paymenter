<?php

namespace App\Admin\Resources\InvoiceResource\Pages;

use App\Admin\Resources\InvoiceResource;
use App\Classes\PDF;
use App\Helpers\SevDeskHelper;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('pdf')
                ->label('Download PDF')
                ->action(function (Invoice $invoice) {
                    if (config('settings.invoice_management') === 'sevdesk') {
                        $sevDeskHelper = new SevDeskHelper();
                        $sevDeskInvoice = $sevDeskHelper->getInvoice($invoice->sevdesk_id);
                        return response()->streamDownload(function () use ($sevDeskInvoice) {
                            echo $sevDeskInvoice['pdf'];
                        }, 'invoice-' . $invoice->id . '.pdf');
                    } else {
                        return response()->streamDownload(function () use ($invoice) {
                            echo PDF::generateInvoice($invoice)->stream();
                        }, 'invoice-' . $invoice->id . '.pdf');
                    }
                }),
        ];
    }
}
