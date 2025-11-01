<?php

return [
    'account' => 'Akun',
    'personal_details' => 'Data pribadi',
    'security' => 'Keamanan',
    'credits' => 'Kredit',

    'change_password' => 'Ubah kata sandi',

    'two_factor_authentication' => 'Autentikasi dua faktor',
    'two_factor_authentication_description' => 'Tambahkan lapisan keamanan ekstra ke akun Anda dengan mengaktifkan autentikasi dua faktor.',
    'two_factor_authentication_enabled' => 'Autentikasi dua faktor diaktifkan untuk akun Anda.',
    'two_factor_authentication_enable' => 'Aktifkan autentikasi dua faktor',
    'two_factor_authentication_disable' => 'Nonaktifkan autentikasi dua faktor',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'Untuk mengaktifkan autentikasi dua faktor, Anda perlu memindai kode QR di bawah ini menggunakan aplikasi autentikator seperti Google Authenticator atau Authy.',
    'two_factor_authentication_qr_code' => 'Pindah kode QR di bawah ini menggunakan aplikasi autentikator anda:',
    'two_factor_authentication_secret' => 'Atau masukkan kode berikut secara manual:',

    'sessions' => 'Sesi',
    'sessions_description' => 'Kelola dan keluar dari sesi aktif Anda pada browser dan perangkat lain.',
    'logout_sessions' => 'Keluar dari sesi ini',

    'input' => [
        'current_password' => 'Kata sandi saat ini',
        'current_password_placeholder' => 'Kata sandi Anda saat ini',
        'new_password' => 'Kata sandi baru',
        'new_password_placeholder' => 'Kata sandi baru Anda',
        'confirm_password' => 'Konfirmasi kata sandi',
        'confirm_password_placeholder' => 'Konfirmasi kata sandi baru Anda',

        'two_factor_code' => 'Masukkan kode dari aplikasi autentikator Anda',
        'two_factor_code_placeholder' => 'Kode autentikasi dua faktor Anda',

        'currency' => 'Mata Uang',
        'amount' => 'Jumlah',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'Kata sandi telah diubah.',
        'password_incorrect' => 'Kata sandi saat ini salah.',
        'two_factor_enabled' => 'Autentikasi dua langkah telah diaktifkan.',
        'two_factor_disabled' => 'Autentikasi dua langkah telah dinonaktifkan.',
        'two_factor_code_incorrect' => 'Kode yang Anda berikan salah.',
        'session_logged_out' => 'Sesi telah dikeluarkan.',
    ],

    'no_credit' => 'Anda tidak memiliki kredit.',
    'add_credit' => 'Tambahkan kredit',
    'credit_deposit' => 'Deposit kredit (:currency)',

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
