<?php

namespace App\Admin\Resources\InvoiceTransactions\Pages;

use App\Admin\Resources\InvoiceTransactions\InvoiceTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvoiceTransactions extends ListRecords
{
    protected static string $resource = InvoiceTransactionResource::class;
}
