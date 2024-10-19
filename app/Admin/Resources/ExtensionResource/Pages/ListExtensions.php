<?php

namespace App\Admin\Resources\ExtensionResource\Pages;

use App\Admin\Resources\ExtensionResource;
use Filament\Resources\Pages\ListRecords;

class ListExtensions extends ListRecords
{
    protected static string $resource = ExtensionResource::class;
}
