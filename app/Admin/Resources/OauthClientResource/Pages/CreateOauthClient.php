<?php

namespace App\Admin\Resources\OauthClientResource\Pages;

use App\Admin\Resources\OauthClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use phpseclib3\Crypt\RSA;
use Laravel\Passport\Passport;

class CreateOauthClient extends CreateRecord
{
    protected static string $resource = OauthClientResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        [$publicKey, $privateKey] = [
            Passport::keyPath('oauth-public.key'),
            Passport::keyPath('oauth-private.key'),
        ];
        // Do the encryption keys exist?
        // Read storage/oauth-private.key and storage/oauth-public.key
        if (!file_exists($publicKey) && !file_exists($privateKey)) {
            $key = RSA::createKey(4096);

            file_put_contents($publicKey, (string) $key->getPublicKey());
            file_put_contents($privateKey, (string) $key);

            if (! windows_os()) {
                chmod($publicKey, 0660);
                chmod($privateKey, 0600);
            }
        }

        $clientRepository = new ClientRepository;
        $record = $clientRepository->create(null, $data['name'], $data['redirect']);

        return $record;
    }
}
