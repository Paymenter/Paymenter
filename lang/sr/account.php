<?php

return [
    'account' => 'Nalog',
    'personal_details' => 'Lični podaci',
    'security' => 'Bezbednost',
    'credits' => 'Krediti',

    'change_password' => 'Promeni lozinku',

    'two_factor_authentication' => 'Dvofaktorska autentifikacija',
    'two_factor_authentication_description' => 'Dodajte dodatni sloj zaštite svom nalogu omogućavanjem dvofaktorske autentifikacije.',
    'two_factor_authentication_enabled' => 'Dvofaktorska autentifikacija je omogućena za vaš nalog.',
    'two_factor_authentication_enable' => 'Omogući dvofaktorsku autentifikaciju',
    'two_factor_authentication_disable' => 'Onemogući dvofaktorsku autentifikaciju',
    'two_factor_authentication_disable_description' => 'Da li ste sigurni da želite da onemogućite dvofaktorsku autentifikaciju?',
    'two_factor_authentication_enable_description' => 'Da biste omogućili dvofaktorsku autentifikaciju, potrebno je da skenirate QR kod ispod pomoću aplikacije za autentifikaciju kao što su Google Authenticator ili Authy.',
    'two_factor_authentication_qr_code' => 'Skenirajte QR kod ispod pomoću aplikacije za autentifikaciju:',
    'two_factor_authentication_secret' => 'Ili ručno unesite sledeći kod:',

    'sessions' => 'Sesije',
    'sessions_description' => 'Upravljajte i odjavite svoje aktivne sesije na drugim pregledačima i uređajima.',
    'logout_sessions' => 'Odjavi ovu sesiju',

    'input' => [
        'current_password' => 'Trenutna lozinka',
        'current_password_placeholder' => 'Vaša trenutna lozinka',
        'new_password' => 'Nova lozinka',
        'new_password_placeholder' => 'Vaša nova lozinka',
        'confirm_password' => 'Potvrdi lozinku',
        'confirm_password_placeholder' => 'Potvrdi trenutnu lozinku',

        'two_factor_code' => 'Unesite kod iz aplikacije za autentifikaciju',
        'two_factor_code_placeholder' => 'Vaš kod za dvostruku autentifikaciju',

        'currency' => 'Valuta',
        'amount' => 'Iznos',
        'payment_gateway' => 'Platni promet',
    ],

    'notifications' => [
        'password_changed' => 'Lozinka je uspešno promenjena',
        'password_incorrect' => 'Trenutna lozinka je netačna.',
        'two_factor_enabled' => 'Dvofaktorska autentifikacija je omogućena.',
        'two_factor_disabled' => 'Dvofaktorska autentifikacija je onemogućena.',
        'two_factor_code_incorrect' => 'Kod je netačan.',
        'session_logged_out' => 'Sesija je izlogovana.',
    ],

    'no_credit' => 'Nemate kredita.',
    'add_credit' => 'Dodaj kredit',
    'credit_deposit' => 'Depozit kredita (:valuta)',

    'payment_methods' => 'Načini plaćanja',
    'recent_transactions' => 'Skorašnje transakcije',
    'saved_payment_methods' => 'Sačuvani načini plaćanja',
    'setup_payment_method' => 'Podesite novi način plaćanja',
    'no_saved_payment_methods' => 'Nemate sačuvane načine plaćanja.',
    'saved_payment_methods_description' => 'Upravljajte svojim sačuvanim načinima plaćanja za bržu porudžbinu i automatska plaćanja.',
    'no_saved_payment_methods_description' => 'Možete dodati način plaćanja kako biste ubuduće obavljali plaćanja brže i lakše, i omogućili automatska plaćanja za svoje usluge.',
    'add_payment_method' => 'Dodaj način plaćanja',
    'payment_method_statuses' => [
        'active' => 'Aktivno',
        'inactive' => 'Neaktivno',
        'expired' => 'Isteklo',
        'pending' => 'Na čekanju',
    ],
    'payment_method_added' => 'Način plaćanja je dodat.',
    'payment_method_add_failed' => 'Dodavanje načina plaćanja nije uspelo. Molimo pokušajte ponovo.',
    'services_linked' => 'Povezano :count usluga',
    'remove' => 'Obriši',
    'remove_payment_method' => 'Obriši metodu plaćanja',
    'remove_payment_method_confirm' => 'Da li ste sigurni da želite da uklonite ":name"? Ova radnja se ne može poništiti.',
    'expires' => 'Ističe :date',
    'cancel' => 'Otkaži',
    'confirm' => 'Da, Ukloni',
    'email_notifications' => 'E-mail obaveštenja',
    'in_app_notifications' => 'Obaveštenja u aplikaciji',
    'notifications_description' => 'Upravljajte svojim postavkama obaveštenja. Možete da odaberete da primate obaveštenja putem e-pošte, u aplikaciji (push) ili na oba načina.',
    'notification' => 'Obaveštenje',

    'push_notifications' => 'Push obaveštenje',
    'push_notifications_description' => 'Omogućite push obaveštenja da primate ažuriranja u realnom vremenu direktno u svom pretraživaču, čak i kada niste na sajtu.',
    'enable_push_notifications' => 'Omogućite Push obaveštenje',
    'push_status' => [
        'not_supported' => 'Push obaveštenja nisu podržana od strane vašeg pretraživača.',
        'denied' => 'Push obaveštenja su blokirana. Molimo vas da ih omogućite u postavkama svog pretraživača.',
        'subscribed' => 'Push obaveštenja su omogućena.',
    ],
];
