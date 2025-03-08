<?php

namespace App\Admin\Resources\HttpLogResource\Pages;

use App\Admin\Resources\HttpLogResource;
use Filament\Resources\Pages\ListRecords;

class ListHttpLogs extends ListRecords
{
    protected static string $resource = HttpLogResource::class;
}
