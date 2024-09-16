<?php

namespace App\Admin\Resources\OauthClientResource\Pages;

use App\Admin\Resources\OauthClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOauthClients extends ListRecords
{
    protected static string $resource = OauthClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
