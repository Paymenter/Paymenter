<?php

namespace App\Admin\Resources\GatewayResource\Pages;

use App\Admin\Resources\GatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGateway extends CreateRecord
{
    protected static string $resource = GatewayResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'gateway';

        return $data;
    }
}
