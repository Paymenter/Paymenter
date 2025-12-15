<?php

return [
    'account' => 'Account',
    'personal_details' => 'Persoonlijke gegevens',
    'security' => 'Beveiliging',
    'credits' => 'Krediet',

    'change_password' => 'Wachtwoord wijzigen',

    'two_factor_authentication' => 'Tweestapsverificatie',
    'two_factor_authentication_description' => 'Voeg een extra beveiligingslaag toe aan je account door tweestapsverificatie in te schakelen.',
    'two_factor_authentication_enabled' => 'Tweestapsverificatie is ingeschakeld voor uw account.',
    'two_factor_authentication_enable' => 'Schakel tweestapsverificatie in',
    'two_factor_authentication_disable' => 'Schakel tweestapsverificatie uit',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'Om tweestapsverificatie in te schakelen, moet u onderstaande QR-code scannen met een authenticatie-app zoals Google Authenticator of Authy.',
    'two_factor_authentication_qr_code' => 'Scan de onderstaande QR-code met uw authenticatie-app:',
    'two_factor_authentication_secret' => 'Of voer de volgende code handmatig in:',

    'sessions' => 'Sessies',
    'sessions_description' => 'Beheer en log uw actieve sessies op andere browsers en apparaten uit.',
    'logout_sessions' => 'Deze sessie afmelden',

    'input' => [
        'current_password' => 'Huidig wachtwoord',
        'current_password_placeholder' => 'Uw huidige wachtwoord',
        'new_password' => 'Nieuw wachtwoord',
        'new_password_placeholder' => 'Uw nieuwe wachtwoord',
        'confirm_password' => 'Wachtwoord bevestigen',
        'confirm_password_placeholder' => 'Bevestig uw nieuwe wachtwoord',

        'two_factor_code' => 'Voer de code uit uw authenticatie-app in',
        'two_factor_code_placeholder' => 'Uw tweestapsverificatiecode',

        'currency' => 'Valuta',
        'amount' => 'Hoeveelheid',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'Wachtwoord is gewijzigd.',
        'password_incorrect' => 'Het huidige wachtwoord is niet correct.',
        'two_factor_enabled' => 'Tweestapsverificatie is ingeschakeld.',
        'two_factor_disabled' => 'Tweestapsverificatie is uitgeschakeld.',
        'two_factor_code_incorrect' => 'De code is incorrect.',
        'session_logged_out' => 'De sessie is uitgelogd.',
    ],

    'no_credit' => 'Je hebt geen krediet.',
    'add_credit' => 'Krediet toevoegen',
    'credit_deposit' => 'Kredietstorting (:currency)',

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
