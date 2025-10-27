<?php

return [
    'account' => 'الحساب',
    'personal_details' => 'البيانات الشخصية',
    'security' => 'الأمان',
    'credits' => 'الرصيد',

    'change_password' => 'تغيير كلمة المرور',

    'two_factor_authentication' => 'المصادقة الثنائية',
    'two_factor_authentication_description' => 'أضف طبقة إضافية من الأمان إلى حسابك بتفعيل المصادقة الثنائية.',
    'two_factor_authentication_enabled' => 'تم تمكين المصادقة الثنائية لحسابك.',
    'two_factor_authentication_enable' => 'تمكين المصادقة الثنائية',
    'two_factor_authentication_disable' => 'تعطيل المصادقة الثنائية',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'لتمكين المصادقة الثنائية، تحتاج إلى مسح رمز QR أدناه باستخدام تطبيق مصادقة مثل Google Authenticator أو Authy.',
    'two_factor_authentication_qr_code' => 'أفحص رمز QR أدناه باستخدام تطبيق المصادقة الخاص بك:',
    'two_factor_authentication_secret' => 'أو أدخل الرمز التالي يدويًا:',

    'sessions' => 'الجلسات',
    'sessions_description' => 'إدارة وتسجيل الخروج من جلساتك النشطة على المتصفحات والأجهزة الأخرى.',
    'logout_sessions' => 'تسجيل خروج هذه الجلسة',

    'input' => [
        'current_password' => 'كلمة المرور الحالية',
        'current_password_placeholder' => 'كلمة مرورك الحالية',
        'new_password' => 'كلمة المرور الجديدة',
        'new_password_placeholder' => 'كلمة مرورك الجديدة',
        'confirm_password' => 'تأكيد كلمة المرور',
        'confirm_password_placeholder' => 'تأكيد كلمة مرورك الجديدة',

        'two_factor_code' => 'أدخل الرمز من تطبيق المصادقة الخاص بك',
        'two_factor_code_placeholder' => 'رمز المصادقة الثنائية الخاص بك',

        'currency' => 'العملة',
        'amount' => 'المبلغ',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'تم تغيير كلمة المرور.',
        'password_incorrect' => 'كلمة المرور الحالية غير صحيحة.',
        'two_factor_enabled' => 'تم تمكين المصادقة الثنائية.',
        'two_factor_disabled' => 'تم تعطيل المصادقة الثنائية.',
        'two_factor_code_incorrect' => 'الرمز غير صحيح.',
        'session_logged_out' => 'تم تسجيل خروج الجلسة.',
    ],

    'no_credit' => 'ليس لديك أي رصيد.',
    'add_credit' => 'إضافة رصيد',
    'credit_deposit' => 'إيداع رصيد (:currency)',

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
