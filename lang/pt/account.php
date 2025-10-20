<?php

return [
    'account' => 'Conta',
    'personal_details' => 'Dados pessoais',
    'security' => 'Segurança',
    'credits' => 'Créditos',

    'change_password' => 'Alterar senha',

    'two_factor_authentication' => 'Autenticação de dois fatores (2FA)',
    'two_factor_authentication_description' => 'Adicione uma camada extra de segurança à sua conta, ativando a autenticação de dois fatores.',
    'two_factor_authentication_enabled' => 'A autenticação de dois fatores está habilitada para sua conta.',
    'two_factor_authentication_enable' => 'Ativar autenticação de dois fatores (2FA)',
    'two_factor_authentication_disable' => 'Desativar autenticação de dois fatores',
    'two_factor_authentication_enable_description' => 'Para habilitar a autenticação de dois fatores, você precisa digitalizar o código QR abaixo com um aplicativo autenticador, como o Autenticador do Google ou Authy.',
    'two_factor_authentication_qr_code' => 'Escaneie o código QR abaixo com o seu aplicativo de autenticação:',
    'two_factor_authentication_secret' => 'Ou insira o seguinte código manualmente:',

    'sessions' => 'Sessões',
    'sessions_description' => 'Gerencie e desconecte suas sessões ativas em outros navegadores e dispositivos.',
    'logout_sessions' => 'Desconectar esta sessão',

    'input' => [
        'current_password' => 'Senha atual',
        'current_password_placeholder' => 'Sua senha atual',
        'new_password' => 'Nova senha',
        'new_password_placeholder' => 'A sua nova senha',
        'confirm_password' => 'Confirme a senha',
        'confirm_password_placeholder' => 'Confirme a sua nova senha',

        'two_factor_code' => 'Insira o código do seu aplicativo autenticador',
        'two_factor_code_placeholder' => 'Seu código de autenticação de dois fatores',

        'currency' => 'Moeda',
        'amount' => 'Quantidade',
        'payment_gateway' => 'Payment gateway',
    ],

    'notifications' => [
        'password_changed' => 'A senha foi alterada.',
        'password_incorrect' => 'Senha incorreta.',
        'two_factor_enabled' => 'A autenticação de dois fatores foi ativada.',
        'two_factor_disabled' => 'A autenticação de dois fatores foi desativada.',
        'two_factor_code_incorrect' => 'O código está incorreto.',
        'session_logged_out' => 'A sessão foi desconectada.',
    ],

    'no_credit' => 'Você não tem créditos.',
    'add_credit' => 'Adicionar crédito',
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
