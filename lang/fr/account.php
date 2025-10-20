<?php

return [
    'account' => 'Compte',
    'personal_details' => 'Détails personnels',
    'security' => 'Sécurité',
    'credits' => 'Crédits',

    'change_password' => 'Modifier le mot de passe',

    'two_factor_authentication' => 'Double authentification',
    'two_factor_authentication_description' => 'Ajoutez une couche de sécurité supplémentaire à votre compte en activant l’authentification à deux facteurs.',
    'two_factor_authentication_enabled' => 'La double authentification est maintenant activée pour votre compte.',
    'two_factor_authentication_enable' => 'Activer la double authentification',
    'two_factor_authentication_disable' => 'Désactiver la double authentification',
    'two_factor_authentication_enable_description' => 'Pour activer l\'authentification à deux facteurs, vous devez scanner le code QR ci-dessous avec une application d\'authentification comme Google Authenticator ou Authy.',
    'two_factor_authentication_qr_code' => 'Scannez le QR code ci-dessous avec votre app d\'authentification :',
    'two_factor_authentication_secret' => 'Ou saisissez le code suivant manuellement :',

    'sessions' => 'Sessions',
    'sessions_description' => 'Gérer et déconnecter vos sessions actives sur les autres navigateurs et appareils.',
    'logout_sessions' => 'Déconnecter cette session',

    'input' => [
        'current_password' => 'Mot de passe actuel',
        'current_password_placeholder' => 'Votre mot de passe actuel',
        'new_password' => 'Nouveau mot de passe',
        'new_password_placeholder' => 'Votre nouveau mot de passe',
        'confirm_password' => 'Confirmation du mot de passe',
        'confirm_password_placeholder' => 'Confirmez votre nouveau mot de passe',

        'two_factor_code' => 'Entrez le code de votre application d\'authentification',
        'two_factor_code_placeholder' => 'Votre code de double authentification',

        'currency' => 'Devise',
        'amount' => 'Montant',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'Le mot de passe a été modifié.',
        'password_incorrect' => 'Le mot de passe actuel est incorrect.',
        'two_factor_enabled' => 'La double authentification a été activée.',
        'two_factor_disabled' => 'La double authentification a été désactivée.',
        'two_factor_code_incorrect' => 'Le code est incorrect.',
        'session_logged_out' => 'La session a été déconnectée.',
    ],

    'no_credit' => 'Vous n\'avez aucun crédit.',
    'add_credit' => 'Ajouter des crédits',
    'credit_deposit' => 'Déposer des crédits (:currency)',

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
