<?php

namespace App\Admin\Resources\GatewayResource\Pages;

use App\Admin\Resources\GatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

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
            $data[$setting->key] = $setting->value;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $gatewaySettings = \Arr::except($data, ['name', 'extension', 'type']);

        $record->update(\Arr::only($data, ['name', 'extension', 'type']));

        foreach ($gatewaySettings as $key => $value) {
            if (!$value) continue;
            $record->settings()->updateOrCreate([
                'key' => $key,
            ], [
                'value' => $value,
            ]);
        }

        return $record;
    }
}
