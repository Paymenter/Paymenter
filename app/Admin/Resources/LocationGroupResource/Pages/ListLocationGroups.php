<?php

namespace App\Admin\Resources\LocationGroupResource\Pages;

use App\Admin\Resources\LocationGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLocationGroups extends ListRecords
{
    protected static string $resource = LocationGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
