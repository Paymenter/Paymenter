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
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
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
        'payment_gateway' => 'Passerelle de paiement',
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

    'payment_methods' => 'Modes de paiement',
    'recent_transactions' => 'Transactions récentes',
    'saved_payment_methods' => 'Méthodes de paiement enregistrées',
    'setup_payment_method' => 'Configurer un nouveau mode de paiement',
    'no_saved_payment_methods' => 'Vous n\'avez actuellement aucun mode de paiement enregistré.',
    'saved_payment_methods_description' => 'Gérez vos modes de paiement enregistrés pour des commandes plus rapide et des paiements automatiques.',
    'no_saved_payment_methods_description' => 'Vous pouvez ajouter une méthode de paiement pour rendre les paiements futurs plus rapides et plus simples. Mais aussi, activer les paiements automatiques pour vos services.',
    'add_payment_method' => 'Ajouter un mode de paiement',
    'payment_method_statuses' => [
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'expired' => 'Expiré',
        'pending' => 'En attente',
    ],
    'payment_method_added' => 'Le mode de paiement a été ajouté.',
    'payment_method_add_failed' => 'Impossible d\'ajouter le mode de paiement. Veuillez réessayer.',
    'services_linked' => ':count service(s) lié(s)',
    'remove' => 'Supprimer',
    'remove_payment_method' => 'Supprimer le moyen de paiement',
    'remove_payment_method_confirm' => 'Êtes-vous sûr de vouloir supprimer :name ? Cette action ne peut être annulée.',
    'expires' => 'Expires :date',
    'cancel' => 'Annuler',
    'confirm' => 'Oui, supprimer',
    'email_notifications' => 'Notifications d’email',
    'in_app_notifications' => 'Notifications dans l’application',
    'notifications_description' => 'Gérez vos préférences de notification. Vous pouvez choisir de recevoir des notifications par e-mail, dans l\'application (push), ou les deux.',
    'notification' => 'Notification',

    'push_notifications' => 'Notifications push',
    'push_notifications_description' => 'Activez les notifications push pour recevoir des mises à jour en temps réel directement dans votre navigateur, même si vous n\'êtes pas sur le site.',
    'enable_push_notifications' => 'Activer les notifications push',
    'push_status' => [
        'not_supported' => 'Les notifications push ne sont pas prises en charge par votre navigateur.',
        'denied' => 'Les notifications push sont bloquées. Veuillez les activer dans les paramètres de votre navigateur.',
        'subscribed' => 'Les notifications push sont activées',
    ],
];
