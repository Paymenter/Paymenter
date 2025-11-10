<?php

return [
    'account' => 'Account',
    'personal_details' => 'Personal details',
    'security' => 'Security',
    'credits' => 'Credits',

    'change_password' => 'Change password',

    'two_factor_authentication' => 'Two-factor authentication',
    'two_factor_authentication_description' => 'Add an extra layer of security to your account by enabling two-factor authentication.',
    'two_factor_authentication_enabled' => 'Two-factor authentication is enabled for your account.',
    'two_factor_authentication_enable' => 'Enable two-factor authentication',
    'two_factor_authentication_disable' => 'Disable two-factor authentication',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'To enable two factor authentication, you need to scan the QR code below with an authenticator app like Google Authenticator or Authy.',
    'two_factor_authentication_qr_code' => 'Scan the QR code below with your authenticator app:',
    'two_factor_authentication_secret' => 'Or enter the following code manually:',

    'sessions' => 'Sessions',
    'sessions_description' => 'Manage and log out your active sessions on other browsers and devices.',
    'logout_sessions' => 'Log this session out',

    'input' => [
        'current_password' => 'Current password',
        'current_password_placeholder' => 'Your current password',
        'new_password' => 'New password',
        'new_password_placeholder' => 'Your new password',
        'confirm_password' => 'Confirm password',
        'confirm_password_placeholder' => 'Confirm your new password',

        'two_factor_code' => 'Enter the code from your authenticator app',
        'two_factor_code_placeholder' => 'Your two-factor authentication code',

        'currency' => 'Currency',
        'amount' => 'Amount',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'Password has been changed.',
        'password_incorrect' => 'The current password is incorrect.',
        'two_factor_enabled' => 'Two-factor authentication has been enabled.',
        'two_factor_disabled' => 'Two-factor authentication has been disabled.',
        'two_factor_code_incorrect' => 'The code is incorrect.',
        'session_logged_out' => 'Session has been logged out.',
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
