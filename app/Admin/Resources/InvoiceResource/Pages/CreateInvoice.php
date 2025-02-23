<?php

namespace App\Admin\Resources\InvoiceResource\Pages;

use App\Admin\Resources\InvoiceResource;
use App\Helpers\NotificationHelper;
use App\Helpers\SevDeskHelper;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $invoice = static::getModel()::create($data);

        if ($data['send_email']) {
            NotificationHelper::invoiceCreatedNotification($invoice->user, $invoice);
        }

        if (config('settings.invoice_management') === 'sevdesk') {
            $sevDeskHelper = new SevDeskHelper();
            $sevDeskInvoiceData = $sevDeskHelper->mapLocalToSevDeskInvoice($invoice);
            $sevDeskInvoice = $sevDeskHelper->createInvoice($sevDeskInvoiceData);
            $invoice->sevdesk_id = $sevDeskInvoice['id'];
            $invoice->save();
        }

        return $invoice;
    }
}
