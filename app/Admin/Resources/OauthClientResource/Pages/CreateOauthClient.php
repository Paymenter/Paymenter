<?php

namespace App\Admin\Resources\OauthClientResource\Pages;

use App\Admin\Resources\OauthClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\ClientRepository;

class CreateOauthClient extends CreateRecord
{
    protected static string $resource = OauthClientResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        array_merge($data, [
            'secret' => \Str::random(40),
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
        ]);
        $record = static::getModel()::create($data);

        $clientRepository = new ClientRepository;
        $clientRepository->create(null, $record->name, $record->redirect);

        return $record;
    }
}
