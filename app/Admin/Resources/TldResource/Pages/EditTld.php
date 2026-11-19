<?php

namespace App\Admin\Resources\TldResource\Pages;

use App\Admin\Resources\TldResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTld extends EditRecord
{
    protected static string $resource = TldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->authorize('delete'),
        ];
    }
}
