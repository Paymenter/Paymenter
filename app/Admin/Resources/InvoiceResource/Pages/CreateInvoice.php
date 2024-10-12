<?php

namespace App\Admin\Resources\InvoiceResource\Pages;

use App\Admin\Resources\InvoiceResource;
use App\Helpers\NotificationHelper;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $invoice = static::getModel()::create($data);

        if ($data['send_email']) {
            NotificationHelper::newInvoiceCreatedNotification($invoice->user, $invoice);
        }

        return $invoice;
    }
}
