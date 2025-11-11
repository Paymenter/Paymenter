<?php

return [
    'services' => 'Tjenester',
    'product' => 'Produkt',
    'price' => 'Pris',
    'status' => 'Status',
    'name' => 'Navn',
    'actions' => 'Handlinger',
    'view' => 'Vis',

    'product_details' => 'Produkt detaljer',
    'billing_cycle' => 'Fakturering intervall',
    'cancel' => 'Kanseller',
    'cancellation' => 'Kansellering av :service',
    'cancel_are_you_sure' => 'Er du sikker på at du vil avbestille denne tjenesten?',
    'cancel_reason' => 'Årsak til avbestilling',
    'cancel_type' => 'Avbestillings Type',
    'cancel_immediate' => 'Avbryt øyeblikkelig',
    'cancel_end_of_period' => 'Avbryt ved slutten av betalings perioden',
    'cancel_immediate_warning' => 'Når du trykker på knappen nedenfor, vil tjenesten kanselleres umiddelbart, og du vil ikke kunne bruke den lenger.',
    'cancellation_requested' => 'Avbestilling forespurt',

    'current_plan' => 'Gjeldende abonnement',
    'new_plan' => 'Nytt abonnement',
    'change_plan' => 'Endre abonnement',
    'current_price' => 'Nåværende pris',
    'new_price' => 'Ny pris',
    'upgrade' => 'Oppgrader',
    'upgrade_summary' => 'Oppgrader sammendrag',
    'total_today' => 'Totalt i dag',
    'upgrade_service' => 'Oppgrader Tjenesten',
    'upgrade_choose_product' => 'Velg et produkt å oppgradere til',
    'upgrade_choose_config' => 'Velg konfigurasjon for oppgraderingen',
    'next_step' => 'Neste trinn',

    'upgrade_pending' => 'Du kan ikke oppgradere mens det allerede er en oppgradering / nedgradert faktura åpen',

    'outstanding_invoice' => 'Du har en utestående faktura.',
    'view_and_pay' => 'Klikk her for å se og betale',

    'statuses' => [
        'pending' => 'Avventer',
        'active' => 'Aktiv',
        'cancelled' => 'Kansellert',
        'suspended' => 'Suspendert',
        'cancellation_pending' => 'Avventer kansellering',
    ],
    'billing_cycles' => [
        'day' => 'dag|dager',
        'week' => 'uke|uker',
        'month' => 'måned|måneder',
        'year' => 'år|år',
    ],
    'every_period' => 'Hver :period :unit',
    'price_every_period' => ':price per :period :unit',
    'price_one_time' => ':price en gang',
    'expires_at' => 'Utløper den',
    'auto_pay' => 'Auto paying using',
    'auto_pay_not_configured' => 'Not configured',

    'no_services' => 'No services found',
    'update_billing_agreement' => 'Update Billing Agreement',
    'clear_billing_agreement' => 'Clear Billing Agreement',
    'select_billing_agreement' => 'Select Billing Agreement',

    'remove_payment_method' => 'Remove Payment Method',
    'remove_payment_method_confirm' => 'Are you sure you want to remove the payment method ":name" from this service? Your service will no longer be able to auto pay its invoices.',
];
