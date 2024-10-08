<?php

namespace App\Admin\Resources\ServiceCancellationResource\Pages;

use App\Admin\Resources\ServiceCancellationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceCancellations extends ListRecords
{
    protected static string $resource = ServiceCancellationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
