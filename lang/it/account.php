<?php

return [
    'account' => 'Profilo',
    'personal_details' => 'Dati Personali',
    'security' => 'Sicurezza',
    'credits' => 'Crediti',

    'change_password' => 'Cambia Password',

    'two_factor_authentication' => 'Autenticazione a due fattori (2FA)',
    'two_factor_authentication_description' => 'Aggiungi un ulteriore livello di sicurezza al tuo account abilitando l\'autenticazione a due fattori.',
    'two_factor_authentication_enabled' => 'L\'autenticazione a più fattori è abilitata per il tuo account.',
    'two_factor_authentication_enable' => 'Abilita autenticazione 2FA',
    'two_factor_authentication_disable' => 'Disabilita l\'autenticazione a due fattori',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'Per abilitare l\'autenticazione a due fattori, è necessario eseguire la scansione del codice QR qui sotto con un\'app di autenticazione come Google Authenticator o Authy.',
    'two_factor_authentication_qr_code' => 'Scansiona il codice QR qui sotto con la tua app di autenticazione:',
    'two_factor_authentication_secret' => 'Oppure inserisci manualmente il seguente codice:',

    'sessions' => 'Sessioni',
    'sessions_description' => 'Gestisci e disconnetti le sessioni attive su altri browser e dispositivi.',
    'logout_sessions' => 'Disconnetti questa sessione',

    'input' => [
        'current_password' => 'Password attuale',
        'current_password_placeholder' => 'Password corrente',
        'new_password' => 'Nuova Password',
        'new_password_placeholder' => 'La tua nuova password',
        'confirm_password' => 'Conferma la password',
        'confirm_password_placeholder' => 'Conferma la tua nuova password',

        'two_factor_code' => 'Inserisci il codice dalla tua app di autenticazione',
        'two_factor_code_placeholder' => 'Il tuo codice di autenticazione a due fattori',

        'currency' => 'Valuta',
        'amount' => 'Quantità',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'La password è stata modificata.',
        'password_incorrect' => 'La password attuale è sbagliata.',
        'two_factor_enabled' => 'L\'autenticazione a due fattori è stata abilitata.',
        'two_factor_disabled' => 'L\'autenticazione a due fattori è stata disabilitata.',
        'two_factor_code_incorrect' => 'Il codice non è corretto ',
        'session_logged_out' => 'Sessione disconnessa.',
    ],

    'no_credit' => 'Non hai crediti.',
    'add_credit' => 'Aggiungi accredito',
    'credit_deposit' => 'Deposito di credito (:currency)',

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
