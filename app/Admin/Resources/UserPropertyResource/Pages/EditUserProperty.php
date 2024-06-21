<?php

namespace App\Admin\Resources\UserPropertyResource\Pages;

use App\Admin\Resources\UserPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserProperty extends EditRecord
{
    protected static string $resource = UserPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
