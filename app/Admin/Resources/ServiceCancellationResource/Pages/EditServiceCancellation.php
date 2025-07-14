<?php

namespace App\Admin\Resources\ServiceCancellationResource\Pages;

use Filament\Actions\DeleteAction;
use App\Admin\Resources\ServiceCancellationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceCancellation extends EditRecord
{
    protected static string $resource = ServiceCancellationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
