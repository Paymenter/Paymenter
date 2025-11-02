<?php

return [
    'account' => 'Benutzerkonto',
    'personal_details' => 'Persönliche Informationen',
    'security' => 'Sicherheit',
    'credits' => 'Guthaben',

    'change_password' => 'Passwort ändern',

    'two_factor_authentication' => 'Zwei-Faktor-Authentifizierung',
    'two_factor_authentication_description' => 'Sichere dein Konto, indem du Zwei-Faktor-Authentifizierung aktivierst.',
    'two_factor_authentication_enabled' => 'Zwei-Faktor-Authentifizierung wurde deinem Account hinzugefügt.',
    'two_factor_authentication_enable' => 'Zwei-Faktor-Authentifizierung aktivieren',
    'two_factor_authentication_disable' => 'Zwei-Faktor-Authentifizierung deaktivieren',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'Zur Aktivierung der Zwei-Faktor-Authentifizierung, müssen Sie den folgenden QR-Code mit einer Authenticator App, wie Google Auth oder Authy, scannen.',
    'two_factor_authentication_qr_code' => 'Scanne den folgenden QR-Code mit deiner Authenticator App:',
    'two_factor_authentication_secret' => 'Oder gib den folgenden Code manuell ein:',

    'sessions' => 'Sitzungen',
    'sessions_description' => 'Verwalte und melde deine aktiven Sitzungen auf anderen Browsern und Geräten ab.',
    'logout_sessions' => 'Diese Sitzung abmelden',

    'input' => [
        'current_password' => 'Aktuelles Passwort',
        'current_password_placeholder' => 'Dein aktuelles Passwort',
        'new_password' => 'Neues Passwort',
        'new_password_placeholder' => 'Neues Passwort',
        'confirm_password' => 'Passwort bestätigen',
        'confirm_password_placeholder' => 'Bestätige dein neues Passwort',

        'two_factor_code' => 'Gib den Code aus deiner Authentifizierungs-App ein',
        'two_factor_code_placeholder' => 'Dein Zwei-Faktor-Authentifizierungscode',

        'currency' => 'Währung',
        'amount' => 'Anzahl',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'Passwort wurde erfolgreich geändert.',
        'password_incorrect' => 'Das aktuelle Passwort ist nicht korrekt.',
        'two_factor_enabled' => 'Zwei-Faktor-Authentifizierung wurde aktiviert.',
        'two_factor_disabled' => 'Zwei-Faktor-Authentifizierung wurde deaktiviert.',
        'two_factor_code_incorrect' => 'Der Code war falsch.',
        'session_logged_out' => 'Sitzung wurde abgemeldet.',
    ],

    'no_credit' => 'Du hast kein Guthaben.',
    'add_credit' => 'Guthaben hinzufügen',
    'credit_deposit' => 'Guthabeneinzahlung (:currency)',

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
    'cancel' => 'Cancel',
    'confirm' => 'Yes, Remove',
    'email_notifications' => 'Email Notifications',
    'in_app_notifications' => 'In-App Notifications',
    'notifications_description' => 'Manage your notification preferences. You can choose to receive notifications via email, in-app (push), or both.',
    'notification' => 'Benachrichtigung',

    'push_notifications' => 'Push Notifications',
    'push_notifications_description' => 'Enable push notifications to receive real-time updates directly in your browser, even when you are not on the site.',
    'enable_push_notifications' => 'Enable Push Notifications',
    'push_status' => [
        'not_supported' => 'Push notifications are not supported by your browser.',
        'denied' => 'Push notifications are blocked. Please enable them in your browser settings.',
        'subscribed' => 'Push notifications are enabled.',
    ],
];
