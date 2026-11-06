<?php

namespace App\Auth;

use Laravel\Passkeys\Actions\GenerateRegistrationOptions;
use Webauthn\PublicKeyCredentialRpEntity;

class RegistrationOptions extends GenerateRegistrationOptions
{
    protected function relyingParty(): PublicKeyCredentialRpEntity
    {
        return PublicKeyCredentialRpEntity::create(
            name: parse_url(config('app.url'), PHP_URL_HOST),
            id: parse_url(config('app.url'), PHP_URL_HOST),
        );
    }
}
