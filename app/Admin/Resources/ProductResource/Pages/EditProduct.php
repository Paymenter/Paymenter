<?php

namespace App\Admin\Resources\ProductResource\Pages;

use App\Admin\Actions\AuditAction;
use App\Admin\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AuditAction::make()->auditChildren(['plans']),
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
        $record->update(\Arr::except($data, ['settings']));

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
