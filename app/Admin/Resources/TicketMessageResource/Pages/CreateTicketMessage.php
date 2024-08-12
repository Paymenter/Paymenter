<?php

namespace App\Admin\Resources\TicketMessageResource\Pages;

use App\Admin\Resources\TicketMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicketMessage extends CreateRecord
{
    protected static string $resource = TicketMessageResource::class;
}
