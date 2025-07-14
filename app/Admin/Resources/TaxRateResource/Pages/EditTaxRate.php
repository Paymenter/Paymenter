<?php

namespace App\Admin\Resources\TaxRateResource\Pages;

use Filament\Actions\DeleteAction;
use App\Admin\Resources\TaxRateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaxRate extends EditRecord
{
    protected static string $resource = TaxRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
