<?php

return [
    'services' => 'Palvelut',
    'product' => 'Tuote',
    'price' => 'Hinta',
    'status' => 'Tilanne',
    'name' => 'Nimi',
    'actions' => 'Toiminnot',
    'view' => 'Näytä',

    'product_details' => 'Tuotetiedot',
    'billing_cycle' => 'Laskutusjakso',
    'cancel' => 'Peruuta',
    'cancellation' => 'Palvelun :service peruminen',
    'cancel_are_you_sure' => 'Oletko varma, että haluat perua tämän palvelen?',
    'cancel_reason' => 'Perumisen syy',
    'cancel_type' => 'Perumisen tyyppi',
    'cancel_immediate' => 'Peruuta välittömästi',
    'cancel_end_of_period' => 'Peruuta tilaus laskutusjakson päättyessä',
    'cancel_immediate_warning' => 'Kun painat alla olevaa painiketta, palvelu perutaan välittömästi, etkä voi enää käyttää sitä.',
    'cancellation_requested' => 'Peruutusta pyydetty',

    'current_plan' => 'Tämänhetkinen tilaus',
    'new_plan' => 'Uusi tilaus',
    'change_plan' => 'Muuta tilausta',
    'current_price' => 'Nykyinen hinta',
    'new_price' => 'Uusi hinta',
    'upgrade' => 'Päivitä',
    'upgrade_summary' => 'Päivityksen yhteenveto',
    'total_today' => 'Yhteensä tänään',
    'upgrade_service' => 'Päivitä palvelu',
    'upgrade_choose_product' => 'Valitse tuote johon haluat päivittää',
    'upgrade_choose_config' => 'Valitse päivityksen konfiguraatio',
    'next_step' => 'Seuraava vaihe',

    'upgrade_pending' => 'Et voi päivittää vaikka päivitys/alennuslasku on jo auki',

    'outstanding_invoice' => 'Sinulla on maksamaton lasku.',
    'view_and_pay' => 'Klikkaa tästä nähdäksesi ja maksaa',

    'statuses' => [
        'pending' => 'Odottaa',
        'active' => 'Aktiivinen',
        'cancelled' => 'Peruttu',
        'suspended' => 'Keskeytetty',
        'cancellation_pending' => 'Cancellation Pending',
    ],
    'billing_cycles' => [
        'day' => 'päivä|päivää',
        'week' => 'viikko|viikkoa',
        'month' => 'kuukausi|kuukautta',
        'year' => 'vuosi|vuotta',
    ],
    'every_period' => 'Joka :period :unit',
    'price_every_period' => ':price per :period :unit',
    'price_one_time' => ':price one time',
    'expires_at' => 'Vanhenee',
    'auto_pay' => 'Auto paying using',
    'auto_pay_not_configured' => 'Not configured',

    'no_services' => 'No services found',
    'update_billing_agreement' => 'Update Billing Agreement',
    'clear_billing_agreement' => 'Clear Billing Agreement',
    'select_billing_agreement' => 'Select Billing Agreement',

    'remove_payment_method' => 'Remove Payment Method',
    'remove_payment_method_confirm' => 'Are you sure you want to remove the payment method ":name" from this service? Your service will no longer be able to auto pay its invoices.',
];
