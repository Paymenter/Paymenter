<?php

namespace App\Admin\Resources\EmailLogResource\Pages;

use App\Admin\Resources\EmailLogResource;
use Filament\Resources\Pages\ListRecords;

class ListEmailLogs extends ListRecords
{
    protected static string $resource = EmailLogResource::class;
}
