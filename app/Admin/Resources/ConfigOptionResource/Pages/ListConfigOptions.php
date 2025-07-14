<?php

namespace App\Admin\Resources\ConfigOptionResource\Pages;

use App\Admin\Resources\ConfigOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConfigOptions extends ListRecords
{
    protected static string $resource = ConfigOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
