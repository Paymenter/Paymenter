<?php

namespace App\Admin\Resources\GatewayResource\Pages;

use App\Admin\Resources\GatewayResource;
use App\Helpers\ExtensionHelper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class EditGateway extends EditRecord
{
    protected static string $resource = GatewayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ($this->record->settings as $setting) {
            $data['settings'][$setting->key] = $setting->value;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update(Arr::except($data, ['settings']));

        if (!isset($data['settings'])) {
            return $record;
        }

        $config = ExtensionHelper::getConfig($record->type, $record->extension);

        $things = array_map(function ($option) use ($data, $record) {
            return [
                'key' => $option['name'],
                'settingable_id' => $record->id,
                'settingable_type' => $record->getMorphClass(),
                'type' => $option['database_type'] ?? 'string',
                'value' => isset($data['settings'][$option['name']]) ? (is_array($data['settings'][$option['name']]) ? json_encode($data['settings'][$option['name']]) : $data['settings'][$option['name']]) : null,
            ];
        }, $config);

        $record->settings()->upsert($things, uniqueBy: [
            'key',
            'settingable_id',
            'settingable_type',
        ], update: [
            'type',
            'value',
        ]);

        return $record;
    }
}
