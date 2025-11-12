<?php

return [
    'account' => 'Hesap',
    'personal_details' => 'Kişisel Bilgiler',
    'security' => 'Güvenlik',
    'credits' => 'Bakiye',

    'change_password' => 'Şifre Değiştir',

    'two_factor_authentication' => 'İki Faktörlü Kimlik Doğrulama',
    'two_factor_authentication_description' => 'İki faktörlü kimlik doğrulamayı etkinleştirerek hesabınıza ek bir güvenlik katmanı ekleyin.',
    'two_factor_authentication_enabled' => 'Hesabınız için iki faktörlü kimlik doğrulama etkinleştirildi.',
    'two_factor_authentication_enable' => 'İki faktörlü kimlik doğrulamayı etkinleştir',
    'two_factor_authentication_disable' => 'İki faktörlü kimlik doğrulamayı devre dışı bırak',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'İki faktörlü kimlik doğrulamayı etkinleştirmek için aşağıdaki QR kodunu Google Authenticator veya Authy gibi bir doğrulayıcı uygulaması ile taramanız gerekiyor.',
    'two_factor_authentication_qr_code' => 'Aşağıdaki QR kodunu doğrulayıcı uygulamanız ile tarayın:',
    'two_factor_authentication_secret' => 'Veya aşağıdaki kodu elle girin:',

    'sessions' => 'Oturumlar',
    'sessions_description' => 'Diğer tarayıcılar ve cihazlardaki aktif oturumlarınızı yönetin ve çıkış yapın.',
    'logout_sessions' => 'Bu oturumdan çıkış yap',

    'input' => [
        'current_password' => 'Mevcut şifre',
        'current_password_placeholder' => 'Mevcut şifreniz',
        'new_password' => 'Yeni şifre',
        'new_password_placeholder' => 'Yeni şifreniz',
        'confirm_password' => 'Şifreyi onayla',
        'confirm_password_placeholder' => 'Yeni şifrenizi onaylayın',

        'two_factor_code' => 'Doğrulayıcı uygulamanızdaki kodu girin',
        'two_factor_code_placeholder' => 'İki faktörlü kimlik doğrulama kodunuz',

        'currency' => 'Para birimi',
        'amount' => 'Tutar',
        'payment_gateway' => 'Ödeme Yöntemi',
    ],

    'notifications' => [
        'password_changed' => 'Şifre değiştirildi.',
        'password_incorrect' => 'Mevcut şifre hatalı.',
        'two_factor_enabled' => 'İki faktörlü kimlik doğrulama etkinleştirildi.',
        'two_factor_disabled' => 'İki faktörlü kimlik doğrulama devre dışı bırakıldı.',
        'two_factor_code_incorrect' => 'Kod hatalı.',
        'session_logged_out' => 'Oturumdan çıkış yapıldı.',
    ],

    'no_credit' => 'Hiç krediniz yok.',
    'add_credit' => 'Kredi ekle',
    'credit_deposit' => 'Kredi yatırma (:currency)',

    'payment_methods' => 'Ödeme Yöntemleri',
    'recent_transactions' => 'Son İşlemler',
    'saved_payment_methods' => 'Kaydedilmiş Ödeme Yöntemleri',
    'setup_payment_method' => 'Set up a new payment method',
    'no_saved_payment_methods' => 'You have no saved payment methods.',
    'saved_payment_methods_description' => 'Manage your saved payment methods for faster checkout and automatic payments.',
    'no_saved_payment_methods_description' => 'You can add a payment method to make future payments faster and easier, and enable automatic payments for your services.',
    'add_payment_method' => 'Add payment method',
    'payment_method_statuses' => [
        'active' => 'Aktif',
        'inactive' => 'Aktif Değil',
        'expired' => 'Süresi doldu',
        'pending' => 'Bekliyor',
    ],
    'payment_method_added' => 'Ödeme yöntemi eklendi.',
    'payment_method_add_failed' => 'Ödeme yöntemi eklenemedi. Lütfen tekrar deneyin.',
    'services_linked' => ':count service(s) linked',
    'remove' => 'Kaldır',
    'remove_payment_method' => 'Ödeme Yöntemini Kaldır',
    'remove_payment_method_confirm' => 'Are you sure you want to remove :name? This action cannot be undone.',
    'expires' => 'Expires :date',
    'cancel' => 'İptal',
    'confirm' => 'Evet, kaldır',
    'email_notifications' => 'E-Posta Bildirimleri',
    'in_app_notifications' => 'Uygulama İçi Bildirimler',
    'notifications_description' => 'Manage your notification preferences. You can choose to receive notifications via email, in-app (push), or both.',
    'notification' => 'Bildirim',

    'push_notifications' => 'Anlık Bildirimler',
    'push_notifications_description' => 'Enable push notifications to receive real-time updates directly in your browser, even when you are not on the site.',
    'enable_push_notifications' => 'Enable Push Notifications',
    'push_status' => [
        'not_supported' => 'Push notifications are not supported by your browser.',
        'denied' => 'Push notifications are blocked. Please enable them in your browser settings.',
        'subscribed' => 'Push notifications are enabled.',
    ],
];
