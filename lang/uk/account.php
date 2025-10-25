<?php

return [
    'account' => 'Account',
    'personal_details' => 'Особисті дані',
    'security' => 'Безпека',
    'credits' => 'Credits',

    'change_password' => 'Змінити пароль',

    'two_factor_authentication' => 'Двофакторна автентифікація',
    'two_factor_authentication_description' => 'Додайте додатковий рівень безпеки до вашого облікового запису, ввімкнувши двофакторну автентифікацію.',
    'two_factor_authentication_enabled' => 'Двофакторна автентифікація ввімкнена для вашого облікового запису.',
    'two_factor_authentication_enable' => 'Увімкнути двофакторну автентифікацію',
    'two_factor_authentication_disable' => 'Вимкнути двофакторну автентифікацію',
    'two_factor_authentication_enable_description' => 'Щоб увімкнути двофакторну автентифікацію, проскануйте QR-код нижче за допомогою додатка для автентифікації, наприклад, Google Authenticator або Authy.',
    'two_factor_authentication_qr_code' => 'Скануйте QR-код нижче у вашому додатку для автентифікації:',
    'two_factor_authentication_secret' => 'Або введіть наступний код вручну:',

    'sessions' => 'Сесії',
    'sessions_description' => 'Керуйте та виходьте зі своїх активних сесій на інших браузерах і пристроях.',
    'logout_sessions' => 'Вийти з цієї сесії',

    'input' => [
        'current_password' => 'Поточний пароль',
        'current_password_placeholder' => 'Ваш поточний пароль',
        'new_password' => 'Новий пароль',
        'new_password_placeholder' => 'Ваш новий пароль',
        'confirm_password' => 'Підтвердити пароль',
        'confirm_password_placeholder' => 'Підтвердьте ваш новий пароль',

        'two_factor_code' => 'Введіть код з вашого додатка для автентифікації',
        'two_factor_code_placeholder' => 'Ваш код двофакторної автентифікації',

        'currency' => 'Currency',
        'amount' => 'Amount',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'Пароль змінено.',
        'password_incorrect' => 'Поточний пароль невірний.',
        'two_factor_enabled' => 'Двофакторна автентифікація ввімкнена.',
        'two_factor_disabled' => 'Двофакторна автентифікація вимкнена.',
        'two_factor_code_incorrect' => 'Код невірний.',
        'session_logged_out' => 'Сесію завершено.',
    ],

    'no_credit' => 'You have no credits.',
    'add_credit' => 'Add credit',
    'credit_deposit' => 'Credit deposit (:currency)',

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
