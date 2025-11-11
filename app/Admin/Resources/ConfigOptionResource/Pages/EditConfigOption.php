<?php

namespace App\Admin\Resources\ConfigOptionResource\Pages;

use App\Admin\Resources\ConfigOptionResource;
use App\Models\ConfigOption;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConfigOption extends EditRecord
{
    protected static string $resource = ConfigOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Show warning when there are child config options
            DeleteAction::make('Delete')
                ->requiresConfirmation()
                ->modalDescription(
                    fn (ConfigOption $record) => $record->serviceConfigs()->exists()
                        ? 'This config option has services connected to it. Deleting it will also delete it from the services it is associated with. Are you sure you want to delete this config option?'
                        : 'Are you sure you want to delete this config option?',
                )
                ->action(function () {
                    $this->record->serviceConfigs()->delete();
                    $this->record->delete();

                    return redirect()->to(ConfigOptionResource::getUrl());
                }),
        ];
    }
}
