<?php

namespace App\Admin\Resources\GatewayResource\Pages;

use Filament\Actions\CreateAction;
use App\Admin\Resources\GatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGateways extends ListRecords
{
    protected static string $resource = GatewayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
