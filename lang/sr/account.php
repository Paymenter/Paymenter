<?php

return [
    'account' => 'Nalog',
    'personal_details' => 'Lični podaci',
    'security' => 'Bezbednost',
    'credits' => 'Krediti',

    'change_password' => 'Promeni lozinku',

    'two_factor_authentication' => 'Dvofaktorska autentifikacija',
    'two_factor_authentication_description' => 'Dodajte dodatni sloj zaštite svom nalogu omogućavanjem dvofaktorske autentifikacije.',
    'two_factor_authentication_enabled' => 'Dvofaktorska autentifikacija je omogućena za vaš nalog.',
    'two_factor_authentication_enable' => 'Omogući dvofaktorsku autentifikaciju',
    'two_factor_authentication_disable' => 'Onemogući dvofaktorsku autentifikaciju',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'Da biste omogućili dvofaktorsku autentifikaciju, potrebno je da skenirate QR kod ispod pomoću aplikacije za autentifikaciju kao što su Google Authenticator ili Authy.',
    'two_factor_authentication_qr_code' => 'Skenirajte QR kod ispod pomoću aplikacije za autentifikaciju:',
    'two_factor_authentication_secret' => 'Ili ručno unesite sledeći kod:',

    'sessions' => 'Sesije',
    'sessions_description' => 'Upravljajte i odjavite svoje aktivne sesije na drugim pregledačima i uređajima.',
    'logout_sessions' => 'Odjavi ovu sesiju',

    'input' => [
        'current_password' => 'Trenutna lozinka',
        'current_password_placeholder' => 'Vaša trenutna lozinka',
        'new_password' => 'Nova lozinka',
        'new_password_placeholder' => 'Vaša nova lozinka',
        'confirm_password' => 'Potvrdi lozinku',
        'confirm_password_placeholder' => 'Potvrdi trenutnu lozinku',

        'two_factor_code' => 'Unesite kod iz aplikacije za autentifikaciju',
        'two_factor_code_placeholder' => 'Vaš kod za dvostruku autentifikaciju',

        'currency' => 'Valuta',
        'amount' => 'Iznos',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'Lozinka je uspešno promenjena',
        'password_incorrect' => 'Trenutna lozinka je netačna.',
        'two_factor_enabled' => 'Dvofaktorska autentifikacija je omogućena.',
        'two_factor_disabled' => 'Dvofaktorska autentifikacija je onemogućena.',
        'two_factor_code_incorrect' => 'Kod je netačan.',
        'session_logged_out' => 'Sesija je izlogovana.',
    ],

    'no_credit' => 'Nemate kredita.',
    'add_credit' => 'Dodaj kredit',
    'credit_deposit' => 'Depozit kredita (:valuta)',

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
