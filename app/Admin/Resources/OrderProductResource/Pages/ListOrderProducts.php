<?php

namespace App\Admin\Resources\OrderProductResource\Pages;

use App\Admin\Resources\OrderProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderProducts extends ListRecords
{
    protected static string $resource = OrderProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
