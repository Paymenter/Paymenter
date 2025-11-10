<?php

return [
    'account' => 'Konto',
    'personal_details' => 'Personlige opplysninger',
    'security' => 'Sikkerhet',
    'credits' => 'Kreditt',

    'change_password' => 'Endre passord',

    'two_factor_authentication' => 'Tofaktor autentisering',
    'two_factor_authentication_description' => 'Legg til et ekstra sikkerhetslag for kontoen din ved å aktivere to-faktor autentisering.',
    'two_factor_authentication_enabled' => 'To-faktor autentisering er aktivert for kontoen din.',
    'two_factor_authentication_enable' => 'Aktiver to-faktor autentisering',
    'two_factor_authentication_disable' => 'Deaktiver to-faktor autentisering',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'For å aktivere to-faktor autentisering, må du skanne QR-koden nedenfor med en autentiseringsapp som Google Authenticator eller Authy.',
    'two_factor_authentication_qr_code' => 'Skann QR-koden nedenfor med din autentiseringsapp:',
    'two_factor_authentication_secret' => 'Eller skriv inn følgende kode manuelt:',

    'sessions' => 'Økter',
    'sessions_description' => 'Administrer og logg ut dine aktive økter i andre nettlesere og enheter.',
    'logout_sessions' => 'Logg ut denne økten',

    'input' => [
        'current_password' => 'Nåværende passord',
        'current_password_placeholder' => 'Ditt nåværende passord',
        'new_password' => 'Nytt passord',
        'new_password_placeholder' => 'Ditt nye passord',
        'confirm_password' => 'Bekreft passord',
        'confirm_password_placeholder' => 'Bekreft nytt passord',

        'two_factor_code' => 'Skriv inn koden fra autentiseringsappen',
        'two_factor_code_placeholder' => 'Din to-faktor autentiseringskode',

        'currency' => 'Valuta',
        'amount' => 'Beløp',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'Passordet er endret.',
        'password_incorrect' => 'Oppgitt passord er feil.',
        'two_factor_enabled' => 'To-faktor autentisering er aktivert.',
        'two_factor_disabled' => 'To-faktor autentisering er deaktivert.',
        'two_factor_code_incorrect' => 'Koden er feil.',
        'session_logged_out' => 'Økten har blitt logget ut.',
    ],

    'no_credit' => 'Du har ingen kreditter.',
    'add_credit' => 'Legg til kreditt',
    'credit_deposit' => 'Kredit innskudd (:currency)',

    'payment_methods' => 'Payment Methods',
    'recent_transactions' => 'Recent Transactions',
    'saved_payment_methods' => 'Saved Payment Methods',
    'setup_payment_method' => 'Set up a new payment method',
    'no_saved_payment_methods' => 'You have no saved payment methods.',
    'saved_payment_methods_description' => 'Manage your saved payment methods for faster checkout and automatic payments.',
    'no_saved_payment_methods_description' => 'You can add a payment method to make future payments faster and easier, and enable automatic payments for your services.',
    'add_payment_method' => 'Add payment method',
    'payment_method_statuses' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'expired' => 'Expired',
        'pending' => 'Pending',
    ],
    'payment_method_added' => 'Payment method has been added.',
    'payment_method_add_failed' => 'Failed to add payment method. Please try again.',
    'services_linked' => ':count service(s) linked',
    'remove' => 'Remove',
    'remove_payment_method' => 'Remove Payment Method',
    'remove_payment_method_confirm' => 'Are you sure you want to remove :name? This action cannot be undone.',
    'expires' => 'Expires :date',
    'cancel' => 'Cancel',
    'confirm' => 'Yes, Remove',
    'email_notifications' => 'Email Notifications',
    'in_app_notifications' => 'In-App Notifications',
    'notifications_description' => 'Manage your notification preferences. You can choose to receive notifications via email, in-app (push), or both.',
    'notification' => 'Notification',

    'push_notifications' => 'Push Notifications',
    'push_notifications_description' => 'Enable push notifications to receive real-time updates directly in your browser, even when you are not on the site.',
    'enable_push_notifications' => 'Enable Push Notifications',
    'push_status' => [
        'not_supported' => 'Push notifications are not supported by your browser.',
        'denied' => 'Push notifications are blocked. Please enable them in your browser settings.',
        'subscribed' => 'Push notifications are enabled.',
    ],
];
