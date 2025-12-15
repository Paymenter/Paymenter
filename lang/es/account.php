<?php

return [
    'account' => 'Cuenta',
    'personal_details' => 'Datos personales',
    'security' => 'Seguridad',
    'credits' => 'Créditos',

    'change_password' => 'Cambiar contraseña',

    'two_factor_authentication' => 'Autenticación de doble factor',
    'two_factor_authentication_description' => 'Añade una capa adicional de seguridad a tu cuenta activando la autenticación de dos factores.',
    'two_factor_authentication_enabled' => 'La autenticación de dos factores está activada en tu cuenta.',
    'two_factor_authentication_enable' => 'Activar la autenticación de doble factor',
    'two_factor_authentication_disable' => 'Desactivar la autenticación de dos factores',
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
    'two_factor_authentication_enable_description' => 'Para habilitar la autenticación de dos factores, debes escanear el código QR a continuación con una aplicación de autenticación como Google Authenticator o Authy.',
    'two_factor_authentication_qr_code' => 'Escanea el código QR a continuación con tu aplicación de autenticación:',
    'two_factor_authentication_secret' => 'O introduce el siguiente código manualmente:',

    'sessions' => 'Sesiones',
    'sessions_description' => 'Administra y cierra sesión en tus sesiones activas en otros navegadores y dispositivos.',
    'logout_sessions' => 'Cerrar esta sesión',

    'input' => [
        'current_password' => 'Contraseña actual',
        'current_password_placeholder' => 'Tu contraseña actual',
        'new_password' => 'Nueva contraseña',
        'new_password_placeholder' => 'Tu nueva contraseña',
        'confirm_password' => 'Confirma la contraseña',
        'confirm_password_placeholder' => 'Confirma tu nueva contraseña',

        'two_factor_code' => 'Ingresa el código de tu aplicación de autenticación',
        'two_factor_code_placeholder' => 'Tu código de autenticación de dos factores',

        'currency' => 'Divisa',
        'amount' => 'Importe',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'La contraseña ha sido cambiada.',
        'password_incorrect' => 'La contraseña actual es incorrecta.',
        'two_factor_enabled' => 'La autenticación de dos factores ha sido habilitada.',
        'two_factor_disabled' => 'La autenticación de dos factores ha sido deshabilitada.',
        'two_factor_code_incorrect' => 'El código es incorrecto.',
        'session_logged_out' => 'La sesión ha sido cerrada.',
    ],

    'no_credit' => 'No tienes créditos.',
    'add_credit' => 'Añadir crédito',
    'credit_deposit' => 'Depósito de crédito (:currency)',

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
