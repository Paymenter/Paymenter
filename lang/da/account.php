<?php

return [
    'account' => 'Konto',
    'personal_details' => 'Personlig information',
    'security' => 'Sikkerhed',
    'credits' => 'Balance',

    'change_password' => 'Skift adgangskode',

    'two_factor_authentication' => 'Totrinsgodkendelse',
    'two_factor_authentication_description' => 'Tilføj et ekstra sikkerhedslag.',
    'two_factor_authentication_enabled' => 'Totrinsgodkendelse er slået til på din konto.',
    'two_factor_authentication_enable' => 'Aktiver totrinsgodkendelse på din konto',
    'two_factor_authentication_disable' => 'Deaktiver totrinsgodkendelse',
    'two_factor_authentication_disable_description' => 'Er du sikker på at du vil deaktivere dette? Det vil fjerne det ekstra sikkerhedslag.',
    'two_factor_authentication_enable_description' => 'For at aktivere totrinsgodkendelse, skal du scanne denne QR kode under med din valgte authenticator app.',
    'two_factor_authentication_qr_code' => 'Scan QR koden under med din valgte authenticator app:',
    'two_factor_authentication_secret' => 'Eller indtast følgende kode manuelt:',

    'sessions' => 'Sessioner',
    'sessions_description' => 'Administrer og log ud af dine aktive sessioner på andre browsere og enheder.',
    'logout_sessions' => 'Log ud af denne session',
    'current_device' => 'Aktuelle enhed',

    'input' => [
        'current_password' => 'Nuværende kodeord',
        'current_password_placeholder' => 'Dit nuværende kodeord',
        'new_password' => 'Nyt kodeord',
        'new_password_placeholder' => 'Dit nye kodeord',
        'confirm_password' => 'Gentag kodeord',
        'confirm_password_placeholder' => 'Gentag dit nye kodeord',

        'two_factor_code' => 'Indtast koden fra din authenticator app',
        'two_factor_code_placeholder' => 'Din totrins kode',

        'currency' => 'Valuta',
        'amount' => 'Beløb',
        'payment_gateway' => 'Betalings portal',
    ],

    'notifications' => [
        'password_changed' => 'Kodeord ændret.',
        'password_incorrect' => 'Kodeordet indtastet stemmer ikke overens med kontoen.',
        'two_factor_enabled' => 'Totrinsgodkendelse er blevet aktiveret.',
        'two_factor_disabled' => 'Totrinsgodkendelse er blevet deaktiveret.',
        'two_factor_code_incorrect' => 'Totrins koden er forkert.',
        'session_logged_out' => 'Sessionen er blevet logget ud.',
    ],

    'no_credit' => 'Du har ingen kreditter på din konto.',
    'add_credit' => 'Tilføj kreditter',
    'credit_deposit' => 'Kredit indskud (:currency)',

    'payment_methods' => 'Betalingsmetoder',
    'recent_transactions' => 'Seneste transaktioner',
    'saved_payment_methods' => 'Gemte betalingsmetoder',
    'setup_payment_method' => 'Opret ny betalingsmetode',
    'no_saved_payment_methods' => 'Du har ingen gemte betalingsmetoder.',
    'saved_payment_methods_description' => 'Administrer dine gemte betalingsmetoder for hurtigere og automatisk betaling.',
    'no_saved_payment_methods_description' => 'Du kan tilføje en betalingsmetode for at gøre fremtidige betalinger hurtigere og nemmere samt aktivere automatisk betaling for dine services.',
    'add_payment_method' => 'Tilføj betalingsmetode',
    'payment_method_statuses' => [
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'expired' => 'Udløbet',
        'pending' => 'Afventer',
    ],
    'payment_method_added' => 'Betalingsmetode er blevet tilføjet.',
    'payment_method_add_failed' => 'Betalingsmetoden kunne ikke tilføjes. Prøv igen, eller kontakt kortudsteder.',
    'services_linked' => ':count service(r) tilknyttet',
    'remove' => 'Fjern',
    'remove_payment_method' => 'Fjern betalingsmetode',
    'remove_payment_method_confirm' => 'Er du  sikker på at du vil fjerne :name? Denne handling kan ikke fortrydes.',
    'expires' => 'Udløber :date',
    'cancel' => 'Annuller',
    'confirm' => 'Ja, fjern',
    'email_notifications' => 'E-mail push notifikationer',
    'in_app_notifications' => 'Notifikationer',
    'notifications_description' => 'Administrer dine præferencer for notifikationer. Du kan vælge at modtage notifikationer via email, app (push) eller begge.',
    'notification' => 'Notifikation',

    'push_notifications' => 'Push notifikationer',
    'push_notifications_description' => 'Aktiver push notifikationer for at modtage realtids opdateringer i din browser, selv når du ikke er på siden.',
    'enable_push_notifications' => 'Aktiver push notifikationer',
    'push_status' => [
        'not_supported' => 'Push notifikationer er ikke understøttet i din browser.',
        'denied' => 'Push notifikationer er blevet blokeret. For at modtage push notifikationer skal du aktivere det i din browser.',
        'subscribed' => 'Push notifikationer er aktiveret.',
    ],
];
