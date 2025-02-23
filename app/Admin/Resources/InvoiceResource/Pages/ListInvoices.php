<?php

namespace App\Admin\Resources\InvoiceResource\Pages;

use App\Admin\Resources\InvoiceResource;
use App\Helpers\SevDeskHelper;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('syncSevDesk')
                ->label('Sync with SevDesk')
                ->action(function () {
                    $sevDeskHelper = new SevDeskHelper();
                    $sevDeskHelper->syncInvoices();
                }),
        ];
    }
}
