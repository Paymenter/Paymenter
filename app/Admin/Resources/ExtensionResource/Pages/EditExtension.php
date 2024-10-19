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
                ExtensionHelper::enableExtension($record);
            } else {
                ExtensionHelper::disableExtension($record);
            }
        }
        $record->update(Arr::except($data, ['settings']));

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
