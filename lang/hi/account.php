<?php

return [
    'account' => 'खाता',
    'personal_details' => 'व्यक्तिगत विवरण',
    'security' => 'सुरक्षा',
    'credits' => 'क्रेडिट्स',

    'change_password' => 'पासवर्ड बदलें',

    'two_factor_authentication' => 'दो-चरणीय प्रमाणीकरण',
    'two_factor_authentication_description' => 'दो-चरणीय प्रमाणीकरण सक्षम करके अपने खाते की सुरक्षा को और मजबूत करें।',
    'two_factor_authentication_enabled' => 'आपके खाते के लिए दो-चरणीय प्रमाणीकरण सक्षम है।',
    'two_factor_authentication_enable' => 'दो-चरणीय प्रमाणीकरण सक्षम करें',
    'two_factor_authentication_disable' => 'दो-चरणीय प्रमाणीकरण अक्षम करें',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'दो-चरणीय प्रमाणीकरण सक्षम करने के लिए, नीचे दिए गए QR कोड को Google Authenticator या Authy जैसे ऐप से स्कैन करें।',
    'two_factor_authentication_qr_code' => 'अपने प्रमाणीकरण ऐप से नीचे दिए गए QR कोड को स्कैन करें:',
    'two_factor_authentication_secret' => 'या नीचे दिए गए कोड को मैन्युअली दर्ज करें:',

    'sessions' => 'सेशंस',
    'sessions_description' => 'अन्य ब्राउज़र और डिवाइस पर अपनी सक्रिय सेशंस को प्रबंधित करें और लॉग आउट करें।',
    'logout_sessions' => 'इस सेशन को लॉग आउट करें',

    'input' => [
        'current_password' => 'वर्तमान पासवर्ड',
        'current_password_placeholder' => 'आपका वर्तमान पासवर्ड',
        'new_password' => 'नया पासवर्ड',
        'new_password_placeholder' => 'आपका नया पासवर्ड',
        'confirm_password' => 'पासवर्ड की पुष्टि करें',
        'confirm_password_placeholder' => 'अपने नए पासवर्ड की पुष्टि करें',

        'two_factor_code' => 'अपने प्रमाणीकरण ऐप से कोड दर्ज करें',
        'two_factor_code_placeholder' => 'आपका दो-चरणीय प्रमाणीकरण कोड',

        'currency' => 'मुद्रा',
        'amount' => 'राशि',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'पासवर्ड बदल दिया गया है।',
        'password_incorrect' => 'वर्तमान पासवर्ड गलत है।',
        'two_factor_enabled' => 'दो-चरणीय प्रमाणीकरण सक्षम किया गया है।',
        'two_factor_disabled' => 'दो-चरणीय प्रमाणीकरण अक्षम कर दिया गया है।',
        'two_factor_code_incorrect' => 'कोड गलत है।',
        'session_logged_out' => 'सेशन को लॉग आउट कर दिया गया है।',
    ],

    'no_credit' => 'आपके पास कोई क्रेडिट नहीं है।',
    'add_credit' => 'क्रेडिट जोड़ें',
    'credit_deposit' => 'क्रेडिट जमा (:currency)',

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
