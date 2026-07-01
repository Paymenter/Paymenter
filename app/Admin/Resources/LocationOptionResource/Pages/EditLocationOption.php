<?php

namespace App\Admin\Resources\LocationOptionResource\Pages;

use App\Admin\Actions\AuditAction;
use App\Admin\Resources\LocationOptionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLocationOption extends EditRecord
{
    protected static string $resource = LocationOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            AuditAction::make(),
        ];
    }
}
