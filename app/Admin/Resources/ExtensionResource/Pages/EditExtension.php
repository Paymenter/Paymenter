<?php

namespace App\Admin\Resources\ExtensionResource\Pages;

use App\Admin\Resources\ExtensionResource;
use App\Helpers\ExtensionHelper;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class EditExtension extends EditRecord
{
    protected static string $resource = ExtensionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ($this->record->settings as $setting) {
            $data['settings'][$setting->key] = $setting->value;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // is the extension being enabled or disabled?
        if ($record->enabled != $data['enabled']) {
            // if the extension is being enabled, we need to run the extension's setup method
            if ($data['enabled']) {
                ExtensionHelper::call($record, 'enabled', [$record], mayFail: true);
            } else {
                ExtensionHelper::call($record, 'disabled', [$record], mayFail: true);
            }
        }

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
