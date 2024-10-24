<?php

namespace App\Admin\Resources\CategoryResource\Pages;

use App\Admin\Resources\CategoryResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->before(function (Actions\DeleteAction $action) {
                if ($this->record->products()->exists() || $this->record->children()->exists()) {
                    Notification::make()
                        ->title('Cannot delete category')
                        ->body('The category has products or children categories.')
                        ->duration(5000)
                        ->icon('ri-error-warning-line')
                        ->danger()
                        ->send();
                    $action->cancel();
                }
            }),
        ];
    }
}
