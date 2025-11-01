<?php

return [
    'services' => 'Tjänster',
    'product' => 'Produkt',
    'price' => 'Pris',
    'status' => 'Status',
    'name' => 'Namn',
    'actions' => 'Åtgärder',
    'view' => 'Visa',

    'product_details' => 'Produktdetaljer',
    'billing_cycle' => 'Faktureringsperiod',
    'cancel' => 'Avbryt',
    'cancellation' => 'Annullering av :service',
    'cancel_are_you_sure' => 'Är du säker på att du vill avbryta den här tjänsten?',
    'cancel_reason' => 'Anledning till annullering',
    'cancel_type' => 'Annulleringstyp',
    'cancel_immediate' => 'Avbryt omedelbart',
    'cancel_end_of_period' => 'Avbryt i slutet av faktureringsperioden',
    'cancel_immediate_warning' => 'När du trycker på knappen nedan kommer tjänsten att avbrytas omedelbart och du kommer inte att kunna använda den längre.',
    'cancellation_requested' => 'Annullering begärd',

    'current_plan' => 'Nuvarande Plan',
    'new_plan' => 'Ny Plan',
    'change_plan' => 'Ändra Plan',
    'current_price' => 'Nuvarande pris',
    'new_price' => 'Nytt pris',
    'upgrade' => 'Uppgradera',
    'upgrade_summary' => 'Sammanfattning av uppgradering',
    'total_today' => 'Totalt idag',
    'upgrade_service' => 'Uppgradera tjänsten',
    'upgrade_choose_product' => 'Välj en produkt att uppgradera till',
    'upgrade_choose_config' => 'Välj konfiguration för uppgraderingen',
    'next_step' => 'Nästa steg',

    'upgrade_pending' => 'Du kan inte uppgradera medan det redan finns en uppgradering/nedgradering faktura öppen',

    'outstanding_invoice' => 'Du har en obetald faktura.',
    'view_and_pay' => 'Klicka här för att se och betala',

    'statuses' => [
        'pending' => 'Väntande',
        'active' => 'Aktiv',
        'cancelled' => 'Annullerad',
        'suspended' => 'Avstängd',
        'cancellation_pending' => 'Avbokning pågår',
    ],
    'billing_cycles' => [
        'day' => 'dag|dagar',
        'week' => 'vecka|veckor',
        'month' => 'månad|månader',
        'year' => 'år|år',
    ],
    'every_period' => 'Varje :period :unit',
    'price_every_period' => ':price per :period :unit',
    'price_one_time' => ':price en gång',
    'expires_at' => 'Utgår den',
    'auto_pay' => 'Auto paying using',
    'auto_pay_not_configured' => 'Not configured',

    'no_services' => 'No services found',
    'update_billing_agreement' => 'Update Billing Agreement',
    'clear_billing_agreement' => 'Clear Billing Agreement',
    'select_billing_agreement' => 'Select Billing Agreement',

    'remove_payment_method' => 'Remove Payment Method',
    'remove_payment_method_confirm' => 'Are you sure you want to remove the payment method ":name" from this service? Your service will no longer be able to auto pay its invoices.',
];
