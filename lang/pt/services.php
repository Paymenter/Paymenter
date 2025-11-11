<?php

return [
    'services' => 'Serviços',
    'product' => 'Produto',
    'price' => 'Preço',
    'status' => 'Situação',
    'name' => 'Nome',
    'actions' => 'Ações',
    'view' => 'Visualizar',

    'product_details' => 'Detalhes do produto',
    'billing_cycle' => 'Ciclo de pagamento',
    'cancel' => 'Cancelar',
    'cancellation' => 'Cancelamento do serviço :service',
    'cancel_are_you_sure' => 'Você tem certeza que deseja cancelar este serviço?',
    'cancel_reason' => 'Motivo do cancelamento',
    'cancel_type' => 'Tipo de cancelamento',
    'cancel_immediate' => 'Cancelar imediato',
    'cancel_end_of_period' => 'Cancelar no fim do período de faturamento',
    'cancel_immediate_warning' => 'Ao clicar no botão abaixo, o serviço será cancelado imediatamente e você não poderá mais usá-lo.',
    'cancellation_requested' => 'Cancelamento solicitado',

    'current_plan' => 'Plano atual',
    'new_plan' => 'Novo plano',
    'change_plan' => 'Alterar plano',
    'current_price' => 'Preço atual',
    'new_price' => 'Novo Preço',
    'upgrade' => 'Atualizar',
    'upgrade_summary' => 'Resumo da atualização',
    'total_today' => 'Total hoje',
    'upgrade_service' => 'Melhorar Serviço',
    'upgrade_choose_product' => 'Escolha um produto para melhorar',
    'upgrade_choose_config' => 'Escolha a configuração para melhorar',
    'next_step' => 'Próximo passo',

    'upgrade_pending' => 'Você não pode fazer outro melhoramento enquanto já há um melhoramento ativo / rebaixamento de fatura aberta',

    'outstanding_invoice' => 'Você tem uma fatura em aberto.',
    'view_and_pay' => 'Clique aqui para ver e pagar',

    'statuses' => [
        'pending' => 'Pendente',
        'active' => 'Ativo',
        'cancelled' => 'Cancelado',
        'suspended' => 'Suspenso',
        'cancellation_pending' => 'Cancelamento Pendente',
    ],
    'billing_cycles' => [
        'day' => 'dia|dias',
        'week' => 'semana|semanas',
        'month' => 'mês|meses',
        'year' => 'ano|anos',
    ],
    'every_period' => 'A cada :periodo :unidade',
    'price_every_period' => ':preço por :periodo :unidade',
    'price_one_time' => ':price única vez',
    'expires_at' => 'Expiração',
    'auto_pay' => 'Pagar automaticamente usando',
    'auto_pay_not_configured' => 'Não configurado',

    'no_services' => 'Nenhum serviço encontrado',
    'update_billing_agreement' => 'Atualizar contrato de cobrança',
    'clear_billing_agreement' => 'Limpar Acordo de Cobrança',
    'select_billing_agreement' => 'Selecionar Acordo de Cobrança',

    'remove_payment_method' => 'Remover Método de Pagamento',
    'remove_payment_method_confirm' => 'Tem certeza que deseja remover o método de pagamento ":name" deste serviço? Seu serviço não será mais capaz de pagar automaticamente as suas faturas.',
];
