<?php

namespace App\Admin\Resources\CustomPropertyResource\Pages;

use Filament\Actions\DeleteAction;
use App\Admin\Resources\CustomPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomProperty extends EditRecord
{
    protected static string $resource = CustomPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
