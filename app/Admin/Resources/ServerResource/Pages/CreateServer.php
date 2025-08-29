<?php

namespace App\Admin\Resources\ServerResource\Pages;

use App\Admin\Resources\ServerResource;
use App\Helpers\ExtensionHelper;
use Arr;
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
        $data['enabled'] = true;
        $record = static::getModel()::create(Arr::except($data, ['settings']));

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

        ExtensionHelper::call($record, 'enabled', [$record], mayFail: true);

        return $record;
    }
}
