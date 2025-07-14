<?php

namespace App\Admin\Resources\TicketMessageResource\Pages;

use Filament\Actions\CreateAction;
use App\Admin\Resources\TicketMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketMessages extends ListRecords
{
    protected static string $resource = TicketMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
