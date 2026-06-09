<?php

namespace App\Admin\Resources\AdjustmentNoteResource\Pages;

use App\Admin\Resources\AdjustmentNoteResource;
use App\Classes\PDF;
use App\Models\AdjustmentNote;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdjustmentNote extends EditRecord
{
    protected static string $resource = AdjustmentNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pdf')
                ->label('Download PDF')
                ->action(function (AdjustmentNote $adjustmentNote) {
                    return response()->streamDownload(function () use ($adjustmentNote) {
                        echo PDF::generateAdjustmentNote($adjustmentNote)->stream();
                    }, 'adjustment-note-' . ($adjustmentNote->number ?? $adjustmentNote->id) . '.pdf');
                }),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
