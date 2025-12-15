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
    'two_factor_authentication_disable_description' => 'Are you sure you want to disable two-factor authentication? This will remove the extra layer of security from your account.',
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
        'payment_gateway' => 'Gateway de pagamento',
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

    'payment_methods' => 'Método de pagamento',
    'recent_transactions' => 'Transações Recentes',
    'saved_payment_methods' => 'Métodos de Pagamento Salvos',
    'setup_payment_method' => 'Configurar um novo método de pagamento',
    'no_saved_payment_methods' => 'Você não tem métodos de pagamento salvos.',
    'saved_payment_methods_description' => 'Gerencie seus métodos de pagamento salvos para finalização de compras mais rápida e pagamentos automáticos.',
    'no_saved_payment_methods_description' => 'Você pode adicionar um método de pagamento para fazer pagamentos futuros mais rápido e fácil, e habilitar pagamentos automáticos para seus serviços.',
    'add_payment_method' => 'Adicionar método de pagamento',
    'payment_method_statuses' => [
        'active' => 'Ativo',
        'inactive' => 'Inativo',
        'expired' => 'Expirado',
        'pending' => 'Pendente',
    ],
    'payment_method_added' => 'Método de pagamento foi adicionado.',
    'payment_method_add_failed' => 'Falha ao adicionar a forma de pagamento. Por favor, tente novamente.',
    'services_linked' => ':count serviço(s) vinculado(s)',
    'remove' => 'Remover',
    'remove_payment_method' => 'Remover Método de Pagamento',
    'remove_payment_method_confirm' => 'Tem certeza que deseja remover :name? Esta ação não pode ser desfeita.',
    'expires' => 'Expires :date',
    'cancel' => 'Cancelar',
    'confirm' => 'Sim, remover',
    'email_notifications' => 'Notificações via E-mail',
    'in_app_notifications' => 'Notificações no Aplicativo',
    'notifications_description' => 'Gerencie suas preferências de notificação. Você pode escolher receber notificações via e-mail, no aplicativo (push), ou ambos.',
    'notification' => 'Notificações',

    'push_notifications' => 'Notificações via Push',
    'push_notifications_description' => 'Ative as notificações push para receber atualizações em tempo real diretamente no seu navegador, mesmo quando você não estiver no site.',
    'enable_push_notifications' => 'Ativar Notificações Push',
    'push_status' => [
        'not_supported' => 'Notificações via push não são suportadas pelo seu navegador.',
        'denied' => 'Notificações push estão bloqueadas. Por favor, habilite-as nas configurações do seu navegador.',
        'subscribed' => 'Notificações via push estão ativadas.',
    ],
];
