<?php

namespace App\Admin\Resources\OauthClientResource\Pages;

use App\Admin\Resources\OauthClientResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use phpseclib3\Crypt\RSA;

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

            if (!windows_os()) {
                chmod($publicKey, 0660);
                chmod($privateKey, 0600);
            }
        }

        $clientRepository = new ClientRepository;
        $record = $clientRepository->createAuthorizationCodeGrantClient($data['name'], explode(',', $data['redirect']));

        $this->js(
            'window.navigator.clipboard.writeText(' . Js::from($record->plainSecret) . ');'
        );

        // Show persisted client secret only once after creation
        Notification::make()
            ->title('OAuth Client Created')
            ->body(Str::markdown("Here is the client secret for the OAuth client you just created. Please copy it now, as it will not be shown again.\n\n Client Secret:\n ```" . $record->plainSecret . '```'))
            ->icon('heroicon-o-lock-closed')
            ->persistent()
            ->success()
            ->send();

        return $record;
    }
}
