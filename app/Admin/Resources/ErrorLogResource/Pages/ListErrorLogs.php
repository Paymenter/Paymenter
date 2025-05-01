<?php

namespace App\Admin\Resources\ErrorLogResource\Pages;

use App\Admin\Resources\ErrorLogResource;
use Filament\Resources\Pages\ListRecords;

class ListErrorLogs extends ListRecords
{
    protected static string $resource = ErrorLogResource::class;
}
