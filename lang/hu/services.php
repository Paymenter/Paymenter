<?php

return [
    'services' => 'Szolgáltatások',
    'product' => 'Termék',
    'price' => 'Ár',
    'status' => 'Állapot',
    'name' => 'Név',
    'actions' => 'Műveletek',
    'view' => 'Megtekintés',

    'product_details' => 'Termék részletei',
    'billing_cycle' => 'Számlázási ciklus',
    'cancel' => 'Mégse',
    'cancellation' => 'A(z) :service lemondása',
    'cancel_are_you_sure' => 'Biztosan szeretné lemondani ezt a szolgáltatást?',
    'cancel_reason' => 'Lemondás oka',
    'cancel_type' => 'Lemondás típusa',
    'cancel_immediate' => 'Azonnali lemondás',
    'cancel_end_of_period' => 'Lemondás a számlázási időszak végén',
    'cancel_immediate_warning' => 'Amikor megnyomod az alábbi gombot, a szolgáltatás azonnal lemondásra kerül, és többé nem fogod tudni használni.',
    'cancellation_requested' => 'Lemondás kérelmezve',

    'current_plan' => 'Jelenlegi csomag',
    'new_plan' => 'Új díjcsomag',
    'change_plan' => 'Díjcsomag változtatás',
    'current_price' => 'Jelenlegi ár',
    'new_price' => 'Új ár',
    'upgrade' => 'Fejlesztés',
    'upgrade_summary' => 'Csomagváltás összefoglalója',
    'total_today' => 'Összesen',
    'upgrade_service' => 'Szolgáltatás bővítése',
    'upgrade_choose_product' => 'Válaszd ki, melyik termékre szeretnél frissíteni',
    'upgrade_choose_config' => 'Válaszd ki a frissítés konfigurációját',
    'next_step' => 'Következő lépés',

    'upgrade_pending' => 'Nem lehet frissíteni, amíg egy frissítési vagy visszalépési számla nyitva van',

    'outstanding_invoice' => 'Van egy kiegyenlítetlen számlád.',
    'view_and_pay' => 'Kattints ide a megtekintéshez és a fizetéshez',

    'statuses' => [
        'pending' => 'Függőben levő',
        'active' => 'Aktív',
        'cancelled' => 'Megszakitva',
        'suspended' => 'Felfüggesztve',
        'cancellation_pending' => 'Lemondás folyamatban',
    ],
    'billing_cycles' => [
        'day' => 'nap',
        'week' => 'hét',
        'month' => 'hónap',
        'year' => 'év',
    ],
    'every_period' => 'Minden :period :unit',
    'price_every_period' => ':price per :period :unit',
    'price_one_time' => ':price one time',
    'expires_at' => 'Lejár ilyenkor',
    'auto_pay' => 'Auto paying using',
    'auto_pay_not_configured' => 'Not configured',

    'no_services' => 'No services found',
    'update_billing_agreement' => 'Update Billing Agreement',
    'clear_billing_agreement' => 'Clear Billing Agreement',
    'select_billing_agreement' => 'Select Billing Agreement',

    'remove_payment_method' => 'Remove Payment Method',
    'remove_payment_method_confirm' => 'Are you sure you want to remove the payment method ":name" from this service? Your service will no longer be able to auto pay its invoices.',
];
