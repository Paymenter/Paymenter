<?php

namespace App\Admin\Resources\LocationOptionResource\Pages;

use App\Admin\Resources\LocationOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLocationOptions extends ListRecords
{
    protected static string $resource = LocationOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
