<?php

namespace App\Admin\Resources\ServiceResource\Pages;

use Filament\Actions\CreateAction;
use App\Admin\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListService extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
