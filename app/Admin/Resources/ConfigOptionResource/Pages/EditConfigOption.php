<?php

namespace App\Admin\Resources\ConfigOptionResource\Pages;

use App\Admin\Resources\ConfigOptionResource;
use App\Admin\Resources\ConfigOptionResource\Concerns\ValidatesDynamicSliderPricing;
use App\Models\ConfigOption;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConfigOption extends EditRecord
{
    use ValidatesDynamicSliderPricing;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->validateDynamicSliderPricing($data);

        // Server-side enforcement: dynamic_slider options can never be upgradable
        // (covers existing rows whose form data still carries upgradable=true).
        if (($data['type'] ?? null) === 'dynamic_slider') {
            $data['upgradable'] = false;
        }

        return $data;
    }
}
