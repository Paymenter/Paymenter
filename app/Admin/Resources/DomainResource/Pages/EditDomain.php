<?php

namespace App\Admin\Resources\DomainResource\Pages;

use App\Admin\Resources\DomainResource;
use App\Models\Domain;
use App\Services\Domain\DomainService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDomain extends EditRecord
{
    protected static string $resource = DomainResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (!$record instanceof Domain) {
            return $record;
        }

        $facts = [];

        foreach (['status', 'expires_at'] as $key) {
            if (array_key_exists($key, $data)) {
                $facts[$key] = $data[$key];
                unset($data[$key]);
            }
        }

        if (array_key_exists('auto_renew', $data)) {
            $record->update(['auto_renew' => (bool) $data['auto_renew']]);
            unset($data['auto_renew']);
        }

        if (!empty($data)) {
            $record->update($data);
        }

        if ($facts) {
            $record = DomainService::apply($record, $facts, 'admin:' . (auth()->id() ?? 'system'));
        }

        return $record;
    }
}
