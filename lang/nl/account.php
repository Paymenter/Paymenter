<?php

return [
    'account' => 'Account',
    'personal_details' => 'Persoonlijke gegevens',
    'security' => 'Beveiliging',
    'credits' => 'Krediet',

    'change_password' => 'Wachtwoord wijzigen',

    'two_factor_authentication' => 'Tweestapsverificatie',
    'two_factor_authentication_description' => 'Voeg een extra beveiligingslaag toe aan je account door tweestapsverificatie in te schakelen.',
    'two_factor_authentication_enabled' => 'Tweestapsverificatie is ingeschakeld voor uw account.',
    'two_factor_authentication_enable' => 'Schakel tweestapsverificatie in',
    'two_factor_authentication_disable' => 'Schakel tweestapsverificatie uit',
    'two_factor_authentication_disable_description' => 'Weet u zeker dat u 2 stapsverificatie wilt uitschakelen? Dit haalt de extra laag van beveiliging weg van uw account. ',
    'two_factor_authentication_enable_description' => 'Om tweestapsverificatie in te schakelen, moet u onderstaande QR-code scannen met een authenticatie-app zoals Google Authenticator of Authy.',
    'two_factor_authentication_qr_code' => 'Scan de onderstaande QR-code met uw authenticatie-app:',
    'two_factor_authentication_secret' => 'Of voer de volgende code handmatig in:',

    'sessions' => 'Sessies',
    'sessions_description' => 'Beheer en log uw actieve sessies op andere browsers en apparaten uit.',
    'logout_sessions' => 'Deze sessie afmelden',
    'current_device' => 'Huidige Apparaat',

    'input' => [
        'current_password' => 'Huidig wachtwoord',
        'current_password_placeholder' => 'Uw huidige wachtwoord',
        'new_password' => 'Nieuw wachtwoord',
        'new_password_placeholder' => 'Uw nieuwe wachtwoord',
        'confirm_password' => 'Wachtwoord bevestigen',
        'confirm_password_placeholder' => 'Bevestig uw nieuwe wachtwoord',

        'two_factor_code' => 'Voer de code uit uw authenticatie-app in',
        'two_factor_code_placeholder' => 'Uw tweestapsverificatiecode',

        'currency' => 'Valuta',
        'amount' => 'Hoeveelheid',
        'payment_gateway' => 'Betaalgateway',
    ],

    'notifications' => [
        'password_changed' => 'Wachtwoord is gewijzigd.',
        'password_incorrect' => 'Het huidige wachtwoord is niet correct.',
        'two_factor_enabled' => 'Tweestapsverificatie is ingeschakeld.',
        'two_factor_disabled' => 'Tweestapsverificatie is uitgeschakeld.',
        'two_factor_code_incorrect' => 'De code is incorrect.',
        'session_logged_out' => 'De sessie is uitgelogd.',
    ],

    'no_credit' => 'Je hebt geen krediet.',
    'add_credit' => 'Krediet toevoegen',
    'credit_deposit' => 'Kredietstorting (:currency)',

    'payment_methods' => 'Betaalmethodes',
    'recent_transactions' => 'Recente Transacties',
    'saved_payment_methods' => 'Opgeslagen Betaalmethodes',
    'setup_payment_method' => 'Maak een nieuwe betaalmethode',
    'no_saved_payment_methods' => 'U heeft geen opgeslagen betaalmethodes.',
    'saved_payment_methods_description' => 'Beheer uw opgeslagen betaalmethodes voor een sneller afrekenen en automatische betalingen.',
    'no_saved_payment_methods_description' => 'U kan een betaalmethode toevoegen om toekomstige betalingen makkelijker te maken en automatische betaling voor uw services in te schakelen.',
    'add_payment_method' => 'Voeg een betaalmethode toe',
    'payment_method_statuses' => [
        'active' => 'Actief',
        'inactive' => 'Inactief',
        'expired' => 'Verlopen',
        'pending' => 'In behandeling',
    ],
    'payment_method_added' => 'De betaalmethode is toegevoegd',
    'payment_method_add_failed' => 'Toevoegen van betalingsmethode mislukt. Probeer het opnieuw.',
    'services_linked' => ':count service(s) verbonden',
    'remove' => 'Verwijder',
    'remove_payment_method' => 'Betaalmethode verwijderen',
    'remove_payment_method_confirm' => 'Weet u zeker dat u {name} wilt verwijderen? Deze handeling kan niet ongedaan worden gemaakt.',
    'expires' => 'Verloopt :date',
    'cancel' => 'Annuleer',
    'confirm' => 'Ja, verwijderen',
    'email_notifications' => 'E-mail notificaties',
    'in_app_notifications' => 'In-app meldingen',
    'notifications_description' => 'Beheer uw voorkeuren voor meldingen. U kunt kiezen om meldingen te ontvangen via e-mail, in-app (push) of beide.',
    'notification' => 'Notificatie',

    'push_notifications' => 'Pushmeldingen',
    'push_notifications_description' => 'Pushmeldingen inschakelen om real-time updates rechtstreeks in uw browser te ontvangen, zelfs als u niet op de website bent.',
    'enable_push_notifications' => 'Pushmeldingen inschakelen',
    'push_status' => [
        'not_supported' => 'Pushmeldingen worden niet ondersteund door uw browser.',
        'denied' => 'Pushmeldingen worden geblokkeerd. Activeer ze in je browserinstellingen.',
        'subscribed' => 'Pushmeldingen zijn ingeschakeld.',
    ],
];
