<?php

return [
    'account' => 'Käyttäjä',
    'personal_details' => 'Henkilökohtaiset tiedot',
    'security' => 'Turvallisuus',
    'credits' => 'Krediitit',

    'change_password' => 'Vaihda salasana',

    'two_factor_authentication' => 'Kaksivaiheinen tunnistautuminen',
    'two_factor_authentication_description' => 'Lisää ylimääräinen suojauskerros tilillesi ottamalla käyttöön kaksivaiheisen tunnistautumisen.',
    'two_factor_authentication_enabled' => 'Kaksivaiheinen todennus on käytössä tililläsi.',
    'two_factor_authentication_enable' => 'Ota käyttöön kaksivaiheinen tunnistautuminen',
    'two_factor_authentication_disable' => 'Poista käytöstä kaksivaiheinen tunnistautuminen',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'Ottaaksesi käyttöön kaksivaiheisen todennuksen, sinun täytyy skannata alla oleva QR-koodi autentikointisovelluksella, kuten Google Authenticator tai Authy.',
    'two_factor_authentication_qr_code' => 'Skannaa alla oleva QR-koodi tunnistautumissovelluksellasi:',
    'two_factor_authentication_secret' => 'Tai kirjoita seuraava koodi manuaalisesti:',

    'sessions' => 'Istunnot',
    'sessions_description' => 'Hallitse ja kirjaudu ulos aktiivisista istunnoistasi muissa selaimissa ja laitteissa.',
    'logout_sessions' => 'Kirjaudu ulos tästä istunnosta',

    'input' => [
        'current_password' => 'Nykyinen salasana',
        'current_password_placeholder' => 'Nykyinen salasanasi',
        'new_password' => 'Uusi salasana',
        'new_password_placeholder' => 'Sinun uusi salasanasi',
        'confirm_password' => 'Vahvista salasana',
        'confirm_password_placeholder' => 'Vahvista uusi salasanasi',

        'two_factor_code' => 'Syötä todennussovelluksen näyttämä koodi',
        'two_factor_code_placeholder' => 'Kaksivaiheinen tunnistautumiskoodi',

        'currency' => 'Valuutta',
        'amount' => 'Summa',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'Salasanasi on vaihdettu.',
        'password_incorrect' => 'Nykyinen salasana on väärin.',
        'two_factor_enabled' => 'Kaksivaiheinen tunnistautuminen on otettu käyttöön.',
        'two_factor_disabled' => 'Kaksivaiheinen tunnistautuminen on poistettu käytöstä.',
        'two_factor_code_incorrect' => 'Koodi on virheellinen.',
        'session_logged_out' => 'Istunto on kirjauduttu ulos.',
    ],

    'no_credit' => 'Sinulla ei ole yhtään krediittejä.',
    'add_credit' => 'Lisää krediittejä',
    'credit_deposit' => 'Krediittien talletus (:currency)',

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
