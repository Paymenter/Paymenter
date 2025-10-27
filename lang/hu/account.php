<?php

return [
    'account' => 'Fiók',
    'personal_details' => 'Személyes adatok',
    'security' => 'Biztonság',
    'credits' => 'Kreditek',

    'change_password' => 'Jelszó módosítása',

    'two_factor_authentication' => 'Kétlépcsős azonosítás',
    'two_factor_authentication_description' => 'Adj egy extra biztonsági réteget a fiókodhoz a kétlépcsős azonosítás engedélyezésével.',
    'two_factor_authentication_enabled' => 'A kétlépcsős azonosítás be van kapcsolva a fiókodhoz.',
    'two_factor_authentication_enable' => 'Kétlépcsős azonosítás engedélyezése',
    'two_factor_authentication_disable' => 'Kétlépcsős azonosítás letiltása',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'A kétlépcsős azonosítás engedélyezéséhez a lenti QR-kódot olvasd be egy autentikátor alkalmazással, például a Google Authenticatorral vagy az Authy-val.',
    'two_factor_authentication_qr_code' => 'Olvasd be az alábbi QR-kódot az autentikátor alkalmazásoddal:',
    'two_factor_authentication_secret' => 'Vagy írd be a következő kódot manuálisan:',

    'sessions' => 'Munkamenetek',
    'sessions_description' => 'Kezeld, és jelentkezz ki aktív munkameneteidből más böngészőkön és eszközökön.',
    'logout_sessions' => 'Ez a munkamenet kijelentkeztetése',

    'input' => [
        'current_password' => 'Jelenlegi jelszó',
        'current_password_placeholder' => 'Jelenlegi jelszavad',
        'new_password' => 'Új jelszó',
        'new_password_placeholder' => 'Új jelszavad',
        'confirm_password' => 'Jelszó megerősítése',
        'confirm_password_placeholder' => 'Erősítsd meg az új jelszavad',

        'two_factor_code' => 'Add meg a hitelesítő alkalmazása által generált kódot',
        'two_factor_code_placeholder' => 'Kétlépcsős azonosító kódod',

        'currency' => 'Pénznem',
        'amount' => 'Mennyiség',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'A jelszó megváltoztatásra került.',
        'password_incorrect' => 'A jelenlegi jelszavad helytelen.',
        'two_factor_enabled' => 'Kétlépcsős azonosítás engedélyezve.',
        'two_factor_disabled' => 'Kétlépcsős azonosítás letiltva.',
        'two_factor_code_incorrect' => 'A kód helytelen.',
        'session_logged_out' => 'A munkamenet ki lett jelentkeztetve.',
    ],

    'no_credit' => 'Nincs kredited.',
    'add_credit' => 'Kredit hozzáadása',
    'credit_deposit' => 'Kredit befizetés (:currency)',

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
