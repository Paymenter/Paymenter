<?php

namespace App\Admin\Resources\AdjustmentNoteResource\Pages;

use App\Admin\Resources\AdjustmentNoteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdjustmentNote extends EditRecord
{
    protected static string $resource = AdjustmentNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
