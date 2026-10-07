<?php

namespace App\Admin\Resources\AdjustmentNoteResource\Pages;

use App\Admin\Resources\AdjustmentNoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdjustmentNotes extends ListRecords
{
    protected static string $resource = AdjustmentNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
