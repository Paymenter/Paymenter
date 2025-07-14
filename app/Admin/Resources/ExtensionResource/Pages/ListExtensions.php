<?php

namespace App\Admin\Resources\ExtensionResource\Pages;

use App\Admin\Resources\ExtensionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListExtensions extends ListRecords
{
    protected static string $resource = ExtensionResource::class;
}
