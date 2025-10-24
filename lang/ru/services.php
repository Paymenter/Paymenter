<?php

return [
    'services' => 'Услуги',
    'product' => 'Продукт',
    'price' => 'Цена',
    'status' => 'Статус',
    'name' => 'Имя',
    'actions' => 'Действия',
    'view' => 'Просмотр',

    'product_details' => 'Детали продукта',
    'billing_cycle' => 'Платежный цикл',
    'cancel' => 'Отмена',
    'cancellation' => 'Отмена :service',
    'cancel_are_you_sure' => 'Вы уверены, что хотите отменить эту услугу?',
    'cancel_reason' => 'Причина отмены',
    'cancel_type' => 'Тип отмены',
    'cancel_immediate' => 'Отменить немедленно',
    'cancel_end_of_period' => 'Отменить в конце оплаченного периода',
    'cancel_immediate_warning' => 'После нажатия кнопки ниже услуга будет немедленно отменена, и вы больше не сможете ей пользоваться.',
    'cancellation_requested' => 'Запрос на отмену отправлен',

    'current_plan' => 'Текущий тариф',
    'new_plan' => 'Новый тариф',
    'change_plan' => 'Сменить тариф',
    'current_price' => 'Текущая цена',
    'new_price' => 'Новая цена',
    'upgrade' => 'Обновить',
    'upgrade_summary' => 'Сводка обновления',
    'total_today' => 'Итого сегодня',
    'upgrade_service' => 'Обновить услугу',
    'upgrade_choose_product' => 'Выберите продукт для обновления',
    'upgrade_choose_config' => 'Выберите конфигурацию для обновления',
    'next_step' => 'Следующий шаг',

    'upgrade_pending' => 'Вы не можете обновить, пока открыт счет на обновление/понижение',

    'outstanding_invoice' => 'У вас есть неоплаченный счет.',
    'view_and_pay' => 'Нажмите здесь, чтобы просмотреть и оплатить',

    'statuses' => [
        'pending' => 'В ожидании',
        'active' => 'Активен',
        'cancelled' => 'Отменен',
        'suspended' => 'Приостановлен',
        'cancellation_pending' => 'Отмена в обработке',
    ],
    'billing_cycles' => [
        'day' => 'день|дня|дней',
        'week' => 'неделя|недели|недель',
        'month' => 'месяц|месяца|месяцев',
        'year' => 'год|года|лет',
    ],
    'every_period' => 'Каждые :period :unit',
    'price_every_period' => ':price за каждые :period :unit',
    'price_one_time' => ':price единовременно',
    'expires_at' => 'Истекает',
];
