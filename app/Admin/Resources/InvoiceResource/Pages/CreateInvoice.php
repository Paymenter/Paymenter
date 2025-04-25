<?php

namespace App\Admin\Resources\InvoiceResource\Pages;

use App\Admin\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $invoice = new (static::getModel());
        $invoice->fill($data);
        $invoice->send_create_email = $data['send_email'] ?? true;
        $invoice->save();

        return $invoice;
    }
}
