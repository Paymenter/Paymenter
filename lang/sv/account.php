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
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'För att aktivera tvåfaktorsautentisering måste du skanna QR-koden nedan med en autentiseringsapp som Google Authenticator eller Authy.',
    'two_factor_authentication_qr_code' => 'Skanna QR-koden nedan med din autentiseringsapp:',
    'two_factor_authentication_secret' => 'Eller ange följande kod manuellt:',

    'sessions' => 'Sessioner',
    'sessions_description' => 'Hantera och logga ut dina aktiva sessioner på andra webbläsare och enheter.',
    'logout_sessions' => 'Logga ut denna session',

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
        'payment_gateway' => 'Payment gateway',
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
