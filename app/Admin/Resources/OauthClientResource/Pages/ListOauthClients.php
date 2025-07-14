<?php

namespace App\Admin\Resources\OauthClientResource\Pages;

use Filament\Actions\CreateAction;
use App\Admin\Resources\OauthClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOauthClients extends ListRecords
{
    protected static string $resource = OauthClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
