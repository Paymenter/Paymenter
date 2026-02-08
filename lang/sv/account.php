<?php

return [
    'account' => 'Konto',
    'personal_details' => 'Personuppgifter',
    'security' => 'Säkerhet',
    'credits' => 'Krediter',

    'change_password' => 'Ändra lösenord',

    'two_factor_authentication' => 'Tvåfaktorautentisering',
    'two_factor_authentication_description' => 'Lägg till ett extra säkerhetslager till ditt konto genom att aktivera tvåfaktorsautentisering.',
    'two_factor_authentication_enabled' => 'Tvåfaktorsautentisering är aktiverat för ditt konto.',
    'two_factor_authentication_enable' => 'Aktivera tvåfaktorsautentisering',
    'two_factor_authentication_disable' => 'Inaktivera tvåfaktorsautentisering',
    'two_factor_authentication_disable_description' => 'Är du säker på att du vill inaktivera tvåfaktorsautentisering? Detta tar bort det extra säkerhetslagret från ditt konto.',
    'two_factor_authentication_enable_description' => 'För att aktivera tvåfaktorsautentisering måste du skanna QR-koden nedan med en autentiseringsapp som Google Authenticator eller Authy.',
    'two_factor_authentication_qr_code' => 'Skanna QR-koden nedan med din autentiseringsapp:',
    'two_factor_authentication_secret' => 'Eller ange följande kod manuellt:',

    'sessions' => 'Sessioner',
    'sessions_description' => 'Hantera och logga ut dina aktiva sessioner på andra webbläsare och enheter.',
    'logout_sessions' => 'Logga ut denna session',
    'current_device' => 'Nuvarande enhet',

    'input' => [
        'current_password' => 'Nuvarande lösenord',
        'current_password_placeholder' => 'Ditt nuvarande lösenord',
        'new_password' => 'Nytt lösenord',
        'new_password_placeholder' => 'Ditt nya lösenord',
        'confirm_password' => 'Bekräfta lösenord',
        'confirm_password_placeholder' => 'Bekräfta ditt nya lösenord',

        'two_factor_code' => 'Ange koden från din autentiseringsapp',
        'two_factor_code_placeholder' => 'Din tvåfaktorsautentiseringskod',

        'currency' => 'Valuta',
        'amount' => 'Belopp',
        'payment_gateway' => 'Betalningsportal',
    ],

    'notifications' => [
        'password_changed' => 'Lösenordet har ändrats.',
        'password_incorrect' => 'Det nuvarande lösenord är felaktigt.',
        'two_factor_enabled' => 'Tvåfaktorsautentisering har aktiverats.',
        'two_factor_disabled' => 'Tvåfaktorsautentisering har inaktiverats.',
        'two_factor_code_incorrect' => 'Koden är felaktig.',
        'session_logged_out' => 'Sessionen har loggats ut.',
    ],

    'no_credit' => 'Du har inga krediter.',
    'add_credit' => 'Lägg till kredit',
    'credit_deposit' => 'Kredit insättning (:currency)',

    'payment_methods' => 'Betalningsmetoder',
    'recent_transactions' => 'Senaste Transaktioner',
    'saved_payment_methods' => 'Sparade betalningsmetoder',
    'setup_payment_method' => 'Skapa en ny betalningsmetod',
    'no_saved_payment_methods' => 'Du har inga sparade betalningsmetoder.',
    'saved_payment_methods_description' => 'Hantera dina sparade betalningsmetoder för snabbare utcheckning och automatiska betalningar.',
    'no_saved_payment_methods_description' => 'Du kan lägga till en betalningsmetod för att göra framtida betalningar snabbare och enklare, och möjliggöra automatiska betalningar för dina tjänster.',
    'add_payment_method' => 'Lägga till en betalningsmetod',
    'payment_method_statuses' => [
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'expired' => 'Löpt ut',
        'pending' => 'Väntande',
    ],
    'payment_method_added' => 'Betalningsmetoden har lagts till.',
    'payment_method_add_failed' => 'Det gick inte att lägga till betalningsmetod. Försök igen.',
    'services_linked' => ':count tjänst(er) länkade',
    'remove' => 'Ta bort',
    'remove_payment_method' => 'Ta bort betalningsmetod',
    'remove_payment_method_confirm' => 'Är du säker på att du vill ta bort :name? Denna åtgärd kan inte ångras.',
    'expires' => 'Utgår den :date',
    'cancel' => 'Avbryt',
    'confirm' => 'Ja, ta bort',
    'email_notifications' => 'E-postaviseringar',
    'in_app_notifications' => 'Notiser i appen',
    'notifications_description' => 'Hantera dina aviseringsinställningar. Du kan välja att ta emot aviseringar via e-post, in-app (push) eller båda.',
    'notification' => 'Notifikation',

    'push_notifications' => 'Pushnotiser',
    'push_notifications_description' => 'Aktivera push-notiser för att ta emot realtidsuppdateringar direkt i din webbläsare, även när du inte är på webbplatsen.',
    'enable_push_notifications' => 'Aktivera Push-Notiser',
    'push_status' => [
        'not_supported' => 'Push-notiser stöds inte av din webbläsare.',
        'denied' => 'Push-notiser är blockerade. Aktivera dem i din webbläsares inställningar.',
        'subscribed' => 'Push-notiser är aktiverade.',
    ],
];
