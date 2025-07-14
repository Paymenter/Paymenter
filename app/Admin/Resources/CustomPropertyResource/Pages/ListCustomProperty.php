<?php

namespace App\Admin\Resources\CustomPropertyResource\Pages;

use Filament\Actions\CreateAction;
use App\Admin\Resources\CustomPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomProperty extends ListRecords
{
    protected static string $resource = CustomPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
