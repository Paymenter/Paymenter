<?php

return [
    'services' => 'Послуги',
    'product' => 'Продукт',
    'price' => 'Ціна',
    'status' => 'Статус',
    'name' => 'Ім\'я',
    'actions' => 'Дії',
    'view' => 'Переглянути',

    'product_details' => 'Деталі продукту',
    'billing_cycle' => 'Цикл оплати',
    'cancel' => 'Скасувати',
    'cancellation' => 'Скасування :service',
    'cancel_are_you_sure' => 'Ви впевнені, що хочете скасувати цю послугу?',
    'cancel_reason' => 'Причина скасування',
    'cancel_type' => 'Тип скасування',
    'cancel_immediate' => 'Скасувати негайно',
    'cancel_end_of_period' => 'Скасувати наприкінці платіжного періоду',
    'cancel_immediate_warning' => 'Коли ви натиснете кнопку нижче, послуга буде скасована негайно, і ви не зможете її більше використовувати.',
    'cancellation_requested' => 'Запит на скасування надіслано',

    'current_plan' => 'Поточний план',
    'new_plan' => 'Новий план',
    'change_plan' => 'Змінити план',
    'current_price' => 'Поточна ціна',
    'new_price' => 'Нова ціна',
    'upgrade' => 'Підвищити',
    'upgrade_summary' => 'Зведення підвищення',
    'total_today' => 'Сума на сьогодні',
    'upgrade_service' => 'Оновлення сервісу',
    'upgrade_choose_product' => 'Виберіть продукт для оновлення',
    'upgrade_choose_config' => 'Виберіть конфігурацію для оновлення',
    'next_step' => 'Наступний крок',

    'upgrade_pending' => 'Ви не можете оновити, поки оновлення вже є відкритий рахунок на нижчому рівні',

    'outstanding_invoice' => 'Є видатний рахунок.',
    'view_and_pay' => 'Натисніть тут, щоб переглянути і оплатити',

    'statuses' => [
        'pending' => 'В очікуванні',
        'active' => 'Активний',
        'cancelled' => 'Скасовано',
        'suspended' => 'Призупинено',
        'cancellation_pending' => 'Очікується скасування',
    ],
    'billing_cycles' => [
        'day' => 'день|дня|днів',
        'week' => 'тиждень|тижня|тижнів',
        'month' => 'місяць|місяця|місяців',
        'year' => 'рік|роки|років',
    ],
    'every_period' => 'Кожні :period :unit',
    'price_every_period' => ':price за :period :unit',
    'price_one_time' => ':price один раз',
    'renews_in' => 'Поновлення через',
    'renews_on' => 'Продовжується до',
    'auto_pay' => 'Автоматична оплата за допомогою',
    'auto_pay_not_configured' => 'Не налаштовано',

    'no_services' => 'Послуги не знайдено',
    'update_billing_agreement' => 'Оновити платіжну угоду',
    'clear_billing_agreement' => 'Скасувати платіжну угоду',
    'select_billing_agreement' => 'Виберіть платіжну угоду',

    'remove_payment_method' => 'Видалити спосіб оплати',
    'remove_payment_method_confirm' => 'Ви дійсно бажаєте видалити платіжний метод ":name" з цього сервісу? Ваша служба більше не зможе автоматично сплачувати рахунки-фактури.',

    'label' => 'Етикетка',
    'label_placeholder' => 'Введіть власну назву для цієї служби',
    'label_modal_title' => 'Редагувати мітку служби',
    'update_label' => 'Оновлення етикетки',

];
