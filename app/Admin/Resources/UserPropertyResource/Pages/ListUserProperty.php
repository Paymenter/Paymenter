<?php

namespace App\Admin\Resources\UserPropertyResource\Pages;

use App\Admin\Resources\UserPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserProperty extends ListRecords
{
    protected static string $resource = UserPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
