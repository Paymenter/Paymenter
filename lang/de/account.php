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
    'two_factor_authentication_disable_description' => 'Sind Sie sicher, dass Sie die Zwei-Faktor-Authentifizierung deaktivieren möchten? Dadurch wird die zusätzliche Sicherheitsebene Ihres Kontos entfernt.',
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
        'amount' => 'Betrag',
        'payment_gateway' => 'Zahlungs-Gateway',
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
    'credit_deposit' => 'Guthaben (:currency)',

    'payment_methods' => 'Zahlungsmethoden',
    'recent_transactions' => 'Kürzliche Transaktionen',
    'saved_payment_methods' => 'Gespeicherte Zahlungsarten',
    'setup_payment_method' => 'Neue Zahlungsmethode einrichten',
    'no_saved_payment_methods' => 'Sie haben keine gespeicherten Zahlungsmethoden.',
    'saved_payment_methods_description' => 'Verwalten Sie Ihre gespeicherten Zahlungsmethoden für schnellere Zahlungsabwicklung und automatische Zahlungen.',
    'no_saved_payment_methods_description' => 'Sie können eine Zahlungsmethode hinzufügen, um zukünftige Zahlungen schneller und einfacher zu machen und automatische Zahlungen für Ihre Dienste zu ermöglichen.',
    'add_payment_method' => 'Zahlungsmethode hinzufügen',
    'payment_method_statuses' => [
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'expired' => 'Abgelaufen',
        'pending' => 'Ausstehend',
    ],
    'payment_method_added' => 'Zahlungsmethode wurde hinzugefügt.',
    'payment_method_add_failed' => 'Fehler beim Hinzufügen der Zahlungsmethode. Bitte erneut versuchen.',
    'services_linked' => ':count Dienst(e) verbunden',
    'remove' => 'Entfernen',
    'remove_payment_method' => 'Zahlungsmethode entfernen',
    'remove_payment_method_confirm' => 'Sind Sie sicher, dass Sie :name entfernen wollen? Diese Aktion kann nicht rückgängig gemacht werden.',
    'expires' => 'Gültig bis :date',
    'cancel' => 'Abbrechen',
    'confirm' => 'Ja, entfernen',
    'email_notifications' => 'E-Mail-Benachrichtigungen',
    'in_app_notifications' => 'In-App-Benachrichtigungen',
    'notifications_description' => 'Verwalten Sie Ihre Benachrichtigungseinstellungen. Sie können Benachrichtigungen per E-Mail, In-App (Push) oder beides empfangen.',
    'notification' => 'Benachrichtigung',

    'push_notifications' => 'Push-Mitteilungen',
    'push_notifications_description' => 'Aktivieren Sie Push-Benachrichtigungen, um Echtzeit-Updates direkt in Ihrem Browser zu erhalten, auch wenn Sie nicht auf der Seite sind.',
    'enable_push_notifications' => 'Push Mitteilungen aktivieren',
    'push_status' => [
        'not_supported' => 'Push-Mitteilungen werden von Ihrem Browser nicht unterstützt.',
        'denied' => 'Push-Mitteilungen sind gesperrt. Bitte aktivieren Sie diese in Ihren Browser-Einstellungen.',
        'subscribed' => 'Push-Mitteilungen sind aktiviert.',
    ],
];
