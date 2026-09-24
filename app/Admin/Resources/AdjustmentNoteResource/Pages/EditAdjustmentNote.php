<?php

namespace App\Admin\Resources\AdjustmentNoteResource\Pages;

use App\Admin\Resources\AdjustmentNoteResource;
use App\Enums\AdjustmentNoteStatus;
use App\Models\AdjustmentNote;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAdjustmentNote extends EditRecord
{
    protected static string $resource = AdjustmentNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('void')
                ->label(fn(AdjustmentNote $record): string => $record->status === AdjustmentNoteStatus::Voided ? 'Unvoid' : 'Void')
                ->color(fn(AdjustmentNote $record): string => $record->status === AdjustmentNoteStatus::Voided ? 'success' : 'danger')
                ->icon(fn(AdjustmentNote $record): string => $record->status === AdjustmentNoteStatus::Voided ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading(fn(AdjustmentNote $record): string => $record->status === AdjustmentNoteStatus::Voided ? 'Unvoid adjustment note' : 'Void adjustment note')
                ->modalDescription(fn(AdjustmentNote $record): string => $record->status === AdjustmentNoteStatus::Voided
                    ? 'Are you sure you want to unvoid this adjustment note? Its amount will be included in the invoice total again.'
                    : 'Are you sure you want to void this adjustment note? Its amount will be excluded from the invoice total.')
                ->modalSubmitActionLabel(fn(AdjustmentNote $record): string => $record->status === AdjustmentNoteStatus::Voided ? 'Unvoid' : 'Void')
                ->action(function (AdjustmentNote $record) {
                    $newStatus = $record->status === AdjustmentNoteStatus::Voided
                        ? AdjustmentNoteStatus::Active
                        : AdjustmentNoteStatus::Voided;
                    $record->update(['status' => $newStatus]);

                    Notification::make()
                        ->title($newStatus === AdjustmentNoteStatus::Voided ? 'Adjustment note voided' : 'Adjustment note unvoided')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
