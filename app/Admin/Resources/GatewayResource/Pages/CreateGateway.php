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
        $record = static::getModel()::create(\Arr::except($data, ['settings']));

        if (!isset($data['settings'])) {
            return $record;
        }

        foreach ($data['settings'] as $key => $value) {
            if (is_null($value)) {
                continue;
            }
            $record->settings()->updateOrCreate([
                'key' => $key,
            ], [
                'value' => $value,
            ]);
        }

        return $record;
    }
}
