<?php

return [
    'services' => 'Usluge',
    'product' => 'Produkti',
    'price' => 'Cena',
    'status' => 'Status',
    'name' => 'Ime',
    'actions' => 'Akcija',
    'view' => 'Pregled',

    'product_details' => 'Detalji proizvoda',
    'billing_cycle' => 'Ciklus naplate',
    'cancel' => 'Otkaži',
    'cancellation' => 'Otkazivanje usluge :service',
    'cancel_are_you_sure' => 'Da li ste sigurni da želite da otkažete ovu uslugu?',
    'cancel_reason' => 'Razlog za otkazivanje',
    'cancel_type' => 'Tip otkazivanja',
    'cancel_immediate' => 'Odmah otkaži',
    'cancel_end_of_period' => 'Otkaži na kraju obračunskog perioda',
    'cancel_immediate_warning' => 'Kada pritisnete dugme ispod, usluga će odmah biti otkazana i više je nećete moći koristiti.',
    'cancellation_requested' => 'Zahtev za otkazivanje je poslat',

    'current_plan' => 'Trenutni paket',
    'new_plan' => 'Novi paket',
    'change_plan' => 'Promeni paket',
    'current_price' => 'Trenutna cena',
    'new_price' => 'Nova cena',
    'upgrade' => 'Nadogradi',
    'upgrade_summary' => 'Pregled nadogradnje',
    'total_today' => 'Ukupno za danas',
    'upgrade_service' => 'Nadogradi uslugu',
    'upgrade_choose_product' => 'Izaberite proizvod za nadogradnju',
    'upgrade_choose_config' => 'Izaberite konfiguraciju za nadogradnju',
    'next_step' => 'Sledeći korak',

    'upgrade_pending' => 'Ne možete izvršiti nadogradnju dok postoji otvorena faktura za nadogradnju ili degradaciju',

    'outstanding_invoice' => 'Imate neplaćenu fakturu.',
    'view_and_pay' => 'Kliknite ovde da pregledate i platite',

    'statuses' => [
        'pending' => 'Na čekanju',
        'active' => 'Aktivno',
        'cancelled' => 'Otkazano',
        'suspended' => 'Suspendovano',
        'cancellation_pending' => 'Otkazivanje na čekanju',
    ],
    'billing_cycles' => [
        'day' => 'dan|dana',
        'week' => 'nedelja|nedelja',
        'month' => 'mesec|meseci',
        'year' => 'godina|godina',
    ],
    'every_period' => 'Svakih :period :unit',
    'price_every_period' => ':price po :period :unit',
    'price_one_time' => ':price jednokratno',
    'expires_at' => 'Ističe',
    'auto_pay' => 'Automatsko plaćanje putem',
    'auto_pay_not_configured' => 'Nije konfigurisano',

    'no_services' => 'Nije pronađena nijedna usluga',
    'update_billing_agreement' => 'Ažuriraj ugovor o naplati',
    'clear_billing_agreement' => 'Ukloni ugovor o naplati',
    'select_billing_agreement' => 'Izaberi ugovor o naplati',

    'remove_payment_method' => 'Ukloni način plaćanja',
    'remove_payment_method_confirm' => 'Da li ste sigurni da želite da uklonite način plaćanja ":name" sa ove usluge? Vaša usluga više neće moći da automatski plaća svoje račune.',
];
