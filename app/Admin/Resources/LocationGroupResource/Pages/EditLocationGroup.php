<?php

namespace App\Admin\Resources\LocationGroupResource\Pages;

use App\Admin\Actions\AuditAction;
use App\Admin\Resources\LocationGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLocationGroup extends EditRecord
{
    protected static string $resource = LocationGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalDescription('Deleting this group keeps its location items and moves them to no group.'),
            AuditAction::make(),
        ];
    }
}
