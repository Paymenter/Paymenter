<?php

namespace App\Admin\Resources\GatewayResource\Pages;

use App\Admin\Resources\GatewayResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateGateway extends CreateRecord
{
    protected static string $resource = GatewayResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'gateway';

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $gatewaySettings = \Arr::except($data, ['name', 'extension', 'type']);

        $model = static::getModel()::create(\Arr::only($data, ['name', 'extension', 'type']));

        foreach ($gatewaySettings as $key => $value) {
            if (!$value) {
                continue;
            }
            $model->settings()->create([
                'key' => $key,
                'value' => $value,
            ]);
        }

        return $model;
    }
}
