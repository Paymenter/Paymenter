<?php

return [
    'account' => '帳戶',
    'personal_details' => '個人資料',
    'security' => '安全性',
    'credits' => '餘額',

    'change_password' => '變更密碼',

    'two_factor_authentication' => '兩步驟驗證',
    'two_factor_authentication_description' => '啟用兩步驟驗證，為您的帳戶增加多一層安全保護。',
    'two_factor_authentication_enabled' => '您的帳戶已啟用兩步驟驗證。',
    'two_factor_authentication_enable' => '啟用兩步驟驗證',
    'two_factor_authentication_disable' => '停用兩步驟驗證',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => '若要啟用兩步驟驗證，您需要使用像 Google Authenticator 或 Authy 這類的驗證應用程式掃描下方的 QR Code。',
    'two_factor_authentication_qr_code' => '使用您的驗證應用程式掃描下方的 QR Code：',
    'two_factor_authentication_secret' => '或手動輸入以下代碼：',

    'sessions' => '工作階段',
    'sessions_description' => '管理並登出您在其他瀏覽器和裝置上的活動工作階段。',
    'logout_sessions' => '登出此工作階段',

    'input' => [
        'current_password' => '目前密碼',
        'current_password_placeholder' => '您目前的密碼',
        'new_password' => '新密碼',
        'new_password_placeholder' => '您的新密碼',
        'confirm_password' => '確認密碼',
        'confirm_password_placeholder' => '確認您的新密碼',

        'two_factor_code' => '輸入您的驗證應用程式中的代碼',
        'two_factor_code_placeholder' => '您的兩步驟驗證代碼',

        'currency' => '貨幣',
        'amount' => '金額',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => '密碼已變更。',
        'password_incorrect' => '目前密碼不正確。',
        'two_factor_enabled' => '兩步驟驗證已啟用。',
        'two_factor_disabled' => '兩步驟驗證已停用。',
        'two_factor_code_incorrect' => '代碼不正確。',
        'session_logged_out' => '工作階段已登出。',
    ],

    'no_credit' => '您沒有餘額。',
    'add_credit' => '新增餘額',
    'credit_deposit' => '餘額儲值 (:currency)',

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
