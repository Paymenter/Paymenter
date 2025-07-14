<?php

namespace App\Admin\Resources\OauthClientResource\Pages;

use Filament\Actions\DeleteAction;
use App\Admin\Resources\OauthClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOauthClient extends EditRecord
{
    protected static string $resource = OauthClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
