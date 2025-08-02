<?php

namespace App\Admin\Resources\CurrencyResource\Pages;

use App\Admin\Resources\CurrencyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCurrency extends EditRecord
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        if (config('settings.default_currency') == $this->record->code) {
            return [];
        }

        return [
            DeleteAction::make(),
        ];
    }
}
