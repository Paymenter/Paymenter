<?php

return [
    'account' => 'Konto',
    'personal_details' => 'Dane osobowe',
    'security' => 'Bezpieczeństwo',
    'credits' => 'Kredyty',

    'change_password' => 'Zmień hasło',

    'two_factor_authentication' => 'Uwierzytelnianie dwuskładnikowe',
    'two_factor_authentication_description' => 'Dodaj dodatkową warstwę zabezpieczeń do swojego konta poprzez włączenie uwierzytelniania dwuskładnikowego.',
    'two_factor_authentication_enabled' => 'Uwierzytelnianie dwuskładnikowe jest włączone dla Twojego konta.',
    'two_factor_authentication_enable' => 'Włącz uwierzytelnianie dwuskładnikowe',
    'two_factor_authentication_disable' => 'Wyłącz uwierzytelnianie dwuskładnikowe',
    'two_factor_authentication_enable_description' => 'Aby włączyć uwierzytelnianie dwuskładnikowe, musisz zeskanować poniższy kod QR za pomocą aplikacji uwierzytelniającej, takiej jak Google Authenticator lub Authy.',
    'two_factor_authentication_qr_code' => 'Zeskanuj poniższy kod QR za pomocą aplikacji uwierzytelniającej:',
    'two_factor_authentication_secret' => 'Lub wprowadź ręcznie następujący kod:',

    'sessions' => 'Sesje',
    'sessions_description' => 'Zarządzaj i wyloguj swoje aktywne sesje na innych przeglądarkach i urządzeniach.',
    'logout_sessions' => 'Wyloguj tę sesję',

    'input' => [
        'current_password' => 'Obecne hasło',
        'current_password_placeholder' => 'Twoje obecne hasło',
        'new_password' => 'Nowe hasło',
        'new_password_placeholder' => 'Twoje nowe hasło',
        'confirm_password' => 'Potwierdź hasło',
        'confirm_password_placeholder' => 'Potwierdź swoje nowe hasło',

        'two_factor_code' => 'Wprowadź kod z aplikacji uwierzytelniającej',
        'two_factor_code_placeholder' => 'Twój kod uwierzytelniania dwuskładnikowego',

        'currency' => 'Waluta',
        'amount' => 'Ilość',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'Hasło zostało zmienione.',
        'password_incorrect' => 'Bieżące hasło jest nieprawidłowe.',
        'two_factor_enabled' => 'Uwierzytelnianie dwustopniowe zostało włączone.',
        'two_factor_disabled' => 'Wyłączono uwierzytelnianie dwuetapowe.',
        'two_factor_code_incorrect' => 'Ten kod jest nieprawidłowy.',
        'session_logged_out' => 'Sesja została wylogowana.',
    ],

    'no_credit' => 'Nie masz żadnych kredytów.',
    'add_credit' => 'Dodaj środki',
    'credit_deposit' => 'Depozyt kredytowy (:currency)',

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
