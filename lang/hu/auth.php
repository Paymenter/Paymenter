<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Ezek az adatok nem egyeznek az adatbázisunkban szereplőkkel.',
    'password' => 'A megadott jelszó helytelen.',
    'throttle' => 'Túl sok bejelentkezési kísérlet. Kérlek, próbáld újra :seconds másodperc múlva.',

    'sign_in' => 'Belépés',
    'sign_in_title' => 'Jelentkezz be a fiókodba',
    'or_sign_in_with' => 'Vagy jelentkezzen be ezzel',
    'forgot_password' => 'Elfelejtetted a jelszavadat?',
    'remember_me' => 'Remember me',
    'dont_have_account' => 'Nincs még fiókod?',
    'already_have_account' => 'Van már fiókod?',

    'sign_up' => 'Regisztráció',
    'sign_up_title' => 'Fiók létrehozása',

    'logout' => 'Kijelentkezés',

    'input' => [
        'email' => 'Email',
        'email_label' => 'Email cím',
        'password' => 'Jelszó',
    ],

    'oauth' => [
        'unverified_discord_account' => 'A Discord fiókod nem hitelesített.',
        'unverified_google_account' => 'Your Google account is not verified.',
        'account_not_registered' => 'Nem regisztráltál ezen az oldalon.',
        'authorization_request' => 'Authorization Request',
        'requesting_permission' => 'is requesting permission to access your account.',
        'scopes_title' => 'This application will be able to:',
        'authorize' => 'Authorize',
    ],

    'reset_password' => 'Jelszó visszaállítása',

    'verify_2fa' => 'Kétfaktoros hitelesítés',
    'verify' => 'Hitelesítés',
    'sign_in_with_passkey' => 'Sign in with a passkey',
    'passkey_signing_in' => 'Waiting for your passkey…',
    'confirm_password_title' => 'Confirm your password',
    'confirm_password_description' => 'For your security, confirm your password before managing passkeys.',
    'confirm_password' => 'Confirm password',

    'verification' => [
        'notice' => 'Erősítsd meg az e-mail címed',
        'sent' => 'Egy új megerősítő linket küldtünk az e-mail címedre.',
        'check_your_email' => 'Mielőtt folytatnád, nézd meg az e-mail-jeidet és keresd meg a megerősítő linket, amit küldtünk.',
        'not_received' => 'Ha nem kaptál megerősítő e-mail-t, itt kérhetsz egy másikat.',
        'request_another' => 'Megerősítő e-mail újraküldése',
    ],
];
