<?php

namespace App\Admin\Resources\TldResource\Pages;

use App\Admin\Resources\TldResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTlds extends ListRecords
{
    protected static string $resource = TldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->authorize('create'),
        ];
    }
}
