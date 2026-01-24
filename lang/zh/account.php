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
    'two_factor_authentication_disable_description' => '您確定要停用雙重認證嗎？這將會移除您帳戶的額外安全防護層。',
    'two_factor_authentication_enable_description' => '若要啟用兩步驟驗證，您需要使用像 Google Authenticator 或 Authy 這類的驗證應用程式掃描下方的 QR Code。',
    'two_factor_authentication_qr_code' => '使用您的驗證應用程式掃描下方的 QR Code：',
    'two_factor_authentication_secret' => '或手動輸入以下代碼：',

    'sessions' => '工作階段',
    'sessions_description' => '管理並登出您在其他瀏覽器和裝置上的活動工作階段。',
    'logout_sessions' => '登出此工作階段',
    'current_device' => '目前裝置',

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
        'payment_gateway' => '支付网关',
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

    'payment_methods' => '付款方式',
    'recent_transactions' => '近期交易',
    'saved_payment_methods' => '已儲存的付款方式',
    'setup_payment_method' => '設定新的付款方式',
    'no_saved_payment_methods' => '您尚未儲存任何繳款方式。',
    'saved_payment_methods_description' => '管理您儲存的付款方式，享有更快速的結帳體驗與自動扣款服務。',
    'no_saved_payment_methods_description' => '您可以新增付款方式，讓未來的支付流程更快速便利，並為您的服務啟用自動扣款功能。',
    'add_payment_method' => '新增付款方式',
    'payment_method_statuses' => [
        'active' => '已啟用',
        'inactive' => '已停用',
        'expired' => '已過期',
        'pending' => '處理中',
    ],
    'payment_method_added' => '已新增付款方式。',
    'payment_method_add_failed' => '無法新增付款方式，請再試一次。',
    'services_linked' => '已連結 :count 個服務',
    'remove' => '移除',
    'remove_payment_method' => '移除付款方式',
    'remove_payment_method_confirm' => '您確定要移除 :name 嗎？此動作將無法復原。',
    'expires' => '有效期限至',
    'cancel' => '取消',
    'confirm' => '確定移除',
    'email_notifications' => '電子郵件通知',
    'in_app_notifications' => '應用程式內通知',
    'notifications_description' => '管理您的通知偏好設定。您可以選擇透過電子郵件、App 內（推播）或同時透過這兩種方式接收通知。',
    'notification' => '通知',

    'push_notifications' => '推送通知',
    'push_notifications_description' => '開啟推播通知，即使不在網站上，也能直接在瀏覽器接收即時更新。',
    'enable_push_notifications' => '啟用推送通知',
    'push_status' => [
        'not_supported' => '您的瀏覽器不支援推播通知。',
        'denied' => '推播通知已被封鎖。請在您的瀏覽器設定中開啟。',
        'subscribed' => '已啟用推播通知',
    ],
];
