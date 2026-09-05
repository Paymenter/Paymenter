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
    'two_factor_authentication_disable_description' => 'İki faktörlü kimlik doğrulamayı devre dışı bırakmak istediğinizden emin misiniz? Bu, hesabınızdaki ek güvenlik katmanını kaldıracaktır.',
    'two_factor_authentication_enable_description' => 'İki faktörlü kimlik doğrulamayı etkinleştirmek için aşağıdaki QR kodunu Google Authenticator veya Authy gibi bir doğrulayıcı uygulaması ile taramanız gerekiyor.',
    'two_factor_authentication_qr_code' => 'Aşağıdaki QR kodunu doğrulayıcı uygulamanız ile tarayın:',
    'two_factor_authentication_secret' => 'Veya aşağıdaki kodu elle girin:',

    'sessions' => 'Oturumlar',
    'sessions_description' => 'Diğer tarayıcılar ve cihazlardaki aktif oturumlarınızı yönetin ve çıkış yapın.',
    'logout_sessions' => 'Bu oturumdan çıkış yap',
    'current_device' => 'Mevcut cihaz',

    'input' => [
        'current_password' => 'Mevcut şifre',
        'current_password_placeholder' => 'Mevcut şifreniz',
        'new_password' => 'Yeni şifre',
        'new_password_placeholder' => 'Yeni şifreniz',
        'confirm_password' => 'Şifreyi onayla',
        'confirm_password_placeholder' => 'Yeni şifrenizi onaylayın',

        'two_factor_code' => 'Kimlik doğrulama uygulamanızdaki kodu girin',
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
    'setup_payment_method' => 'Yeni bir ödeme yöntemi oluşturun',
    'no_saved_payment_methods' => 'You have no saved payment methods.',
    'saved_payment_methods_description' => 'Manage your saved payment methods for faster checkout and automatic payments.',
    'no_saved_payment_methods_description' => 'You can add a payment method to make future payments faster and easier, and enable automatic payments for your services.',
    'add_payment_method' => 'Add payment method',
    'payment_method_statuses' => [
        'active' => 'Active',
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
    'notifications_description' => 'Bildirim tercihlerinizi yönetin. Bildirimleri e-posta yoluyla, uygulama içi (push) olarak veya her ikisiyle birden almayı seçebilirsiniz.',
    'notification' => 'Bildirim',

    'push_notifications' => 'Anlık Bildirimler',
    'push_notifications_description' => 'Push bildirimlerini etkinleştirerek, sitede olmadığınız zamanlarda bile tarayıcınızda doğrudan gerçek zamanlı güncellemeler alabilirsiniz.',
    'enable_push_notifications' => 'Push Bildirimlerini Etkinleştir',
    'push_status' => [
        'not_supported' => 'Tarayıcınız push bildirimlerini desteklemiyor.',
        'denied' => 'Push bildirimler engellenmiştir. Lütfen tarayıcı ayarlarınızdan bunları etkinleştirin.',
        'subscribed' => 'Push bildirimler etkinleştirildi.',
    ],
];
