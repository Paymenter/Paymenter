<?php

namespace App\Admin\Resources\FailedJobResource\Pages;

use App\Admin\Resources\FailedJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFailedJobs extends ListRecords
{
    protected static string $resource = FailedJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
