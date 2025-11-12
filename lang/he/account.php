<?php

return [
    'account' => 'חשבון',
    'personal_details' => 'פרטים אישיים',
    'security' => 'אבטחה',
    'credits' => 'נקודות',

    'change_password' => 'שנה סיסמא',

    'two_factor_authentication' => 'אימות דו שלבי',
    'two_factor_authentication_description' => 'הוסף שכבת אבטחה נוספת על ידי הפעלת אימות דו שלבי.',
    'two_factor_authentication_enabled' => 'אימות דו שלבי מופעל בחשבונך.',
    'two_factor_authentication_enable' => 'הפעל אימות דו שלבי',
    'two_factor_authentication_disable' => 'בטל אימות דו שלבי',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'על מנת להפעיל אימות דו שלבי, עליך לסרוק את קוד ה-QR המצורף באמצעות אפליקציית אימות דו שלבי כמו Google Authenticator או Authy.',
    'two_factor_authentication_qr_code' => 'סרוק את הקוד המצורף באמצעות האפליקציה:',
    'two_factor_authentication_secret' => 'או הכנס את הקוד באופן ידני:',

    'sessions' => 'חיבורים',
    'sessions_description' => 'ניהול וניתוק חיבורים קיימים בדפדפנים ומכשירים אחרים.',
    'logout_sessions' => 'ניתוק החיבור הנוכחי',

    'input' => [
        'current_password' => 'הסיסמה הנוכחית',
        'current_password_placeholder' => 'סיסמתך הנוכחית',
        'new_password' => 'סיסמה חדשה',
        'new_password_placeholder' => 'הסיסמה החדשה שלך היא',
        'confirm_password' => 'אימות סיסמה',
        'confirm_password_placeholder' => 'אמת את הסיסמה החדשה שלך',

        'two_factor_code' => 'הקש את הקוד מיישום האימות שלך',
        'two_factor_code_placeholder' => 'הקוד שלך לאימות דו שלבי',

        'currency' => 'מטבע',
        'amount' => 'סכום',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'הסיסמה השתנתה.',
        'password_incorrect' => 'הסיסמה הנוכחית איננה נכונה.',
        'two_factor_enabled' => 'אימות דו שלבי הופעל.',
        'two_factor_disabled' => 'אימות דו שלבי בוטל.',
        'two_factor_code_incorrect' => 'הקוד אינו נכון.',
        'session_logged_out' => 'החיבור נותק.',
    ],

    'no_credit' => 'נגמרו לך הנקודות.',
    'add_credit' => 'הוסף נקודות',
    'credit_deposit' => 'טעינת נקודות (:currency)',

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
