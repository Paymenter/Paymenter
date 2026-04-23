<?php

namespace App\Admin\Resources\ConfigOptionResource\Pages;

use App\Admin\Resources\ConfigOptionResource;
use App\Admin\Resources\ConfigOptionResource\Concerns\ValidatesDynamicSliderPricing;
use Filament\Resources\Pages\CreateRecord;

class CreateConfigOption extends CreateRecord
{
    use ValidatesDynamicSliderPricing;

    protected static string $resource = ConfigOptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->validateDynamicSliderPricing($data);

        // Server-side enforcement: dynamic_slider options can never be upgradable
        // (the UI also hides this toggle, but persisted form data could still carry it).
        if (($data['type'] ?? null) === 'dynamic_slider') {
            $data['upgradable'] = false;
        }

        return $data;
    }
}
