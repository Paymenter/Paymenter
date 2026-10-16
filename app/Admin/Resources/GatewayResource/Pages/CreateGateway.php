<?php

namespace App\Admin\Resources\GatewayResource\Pages;

use App\Admin\Resources\GatewayResource;
use App\Helpers\ExtensionHelper;
use Arr;
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
        $data['enabled'] = true;
        $record = static::getModel()::create(Arr::except($data, ['settings']));

        if (!isset($data['settings'])) {
            return $record;
        }

        // Same storage rules as the edit page: secrets declared with 'encrypted' => true are never stored in plain text
        $options = collect(ExtensionHelper::getConfig($record->type, $record->extension))->keyBy('name');

        foreach ($data['settings'] as $key => $value) {
            if (is_null($value)) {
                continue;
            }
            $record->settings()->updateOrCreate([
                'key' => $key,
            ], [
                'type' => $options[$key]['database_type'] ?? 'string',
                'value' => is_array($value) ? json_encode($value) : $value,
                'encrypted' => $options[$key]['encrypted'] ?? false,
            ]);
        }

        ExtensionHelper::call($record, 'enabled', [$record], mayFail: true);

        return $record;
    }
}
