<?php

namespace App\Admin\Resources\ServerResource\Pages;

use App\Admin\Resources\ServerResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateServer extends CreateRecord
{
    protected static string $resource = ServerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'server';

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
            ], [
                'value' => $value,
            ]);
        }

        return $model;
    }
}
