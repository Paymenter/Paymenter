<?php

return [
    'account' => 'Käyttäjä',
    'personal_details' => 'Henkilökohtaiset tiedot',
    'security' => 'Turvallisuus',
    'credits' => 'Krediitit',

    'change_password' => 'Vaihda salasana',

    'two_factor_authentication' => 'Kaksivaiheinen tunnistautuminen',
    'two_factor_authentication_description' => 'Lisää ylimääräinen suojauskerros tilillesi ottamalla käyttöön kaksivaiheisen tunnistautumisen.',
    'two_factor_authentication_enabled' => 'Kaksivaiheinen todennus on käytössä tililläsi.',
    'two_factor_authentication_enable' => 'Ota käyttöön kaksivaiheinen tunnistautuminen',
    'two_factor_authentication_disable' => 'Poista käytöstä kaksivaiheinen tunnistautuminen',
    'two_factor_authentication_disable_description' => 'Oletko varma, että haluat poistaa kaksivaiheisen todennuksen käytöstä? Tämä poistaa ylimääräisen suojaustason tililtäsi.',
    'two_factor_authentication_enable_description' => 'Ottaaksesi käyttöön kaksivaiheisen todennuksen, sinun täytyy skannata alla oleva QR-koodi autentikointisovelluksella, kuten Google Authenticator tai Authy.',
    'two_factor_authentication_qr_code' => 'Skannaa alla oleva QR-koodi tunnistautumissovelluksellasi:',
    'two_factor_authentication_secret' => 'Tai kirjoita seuraava koodi manuaalisesti:',

    'sessions' => 'Istunnot',
    'sessions_description' => 'Hallitse ja kirjaudu ulos aktiivisista istunnoistasi muissa selaimissa ja laitteissa.',
    'logout_sessions' => 'Kirjaudu ulos tästä istunnosta',
    'current_device' => 'Tämänhetkinen laite',

    'input' => [
        'current_password' => 'Nykyinen salasana',
        'current_password_placeholder' => 'Nykyinen salasanasi',
        'new_password' => 'Uusi salasana',
        'new_password_placeholder' => 'Sinun uusi salasanasi',
        'confirm_password' => 'Vahvista salasana',
        'confirm_password_placeholder' => 'Vahvista uusi salasanasi',

        'two_factor_code' => 'Syötä todennussovelluksen näyttämä koodi',
        'two_factor_code_placeholder' => 'Kaksivaiheinen tunnistautumiskoodi',

        'currency' => 'Valuutta',
        'amount' => 'Summa',
        'payment_gateway' => 'Maksupalvelu',
    ],

    'notifications' => [
        'password_changed' => 'Salasanasi on vaihdettu.',
        'password_incorrect' => 'Nykyinen salasana on väärin.',
        'two_factor_enabled' => 'Kaksivaiheinen tunnistautuminen on otettu käyttöön.',
        'two_factor_disabled' => 'Kaksivaiheinen tunnistautuminen on poistettu käytöstä.',
        'two_factor_code_incorrect' => 'Koodi on virheellinen.',
        'session_logged_out' => 'Istunto on kirjauduttu ulos.',
    ],

    'no_credit' => 'Sinulla ei ole yhtään krediittejä.',
    'add_credit' => 'Lisää krediittejä',
    'credit_deposit' => 'Krediittien talletus (:currency)',

    'payment_methods' => 'Maksutavat',
    'recent_transactions' => 'Viimeisimmät tapahtumat',
    'saved_payment_methods' => 'Tallennetut maksutavat',
    'setup_payment_method' => 'Aseta uusi maksutapa',
    'no_saved_payment_methods' => 'Sinulla ei ole tallennettuja maksutapoja.',
    'saved_payment_methods_description' => 'Hallitse tallennettuja maksutapojasi nopeammille ostoksille ja automaattimaksuille.',
    'no_saved_payment_methods_description' => 'Voit lisätä maksutavan tehdäksesi tulevista maksuista nopeampia ja helpompia sekä salliaksesi palvelujesi automaattiset maksut.',
    'add_payment_method' => 'Lisää maksutapa',
    'payment_method_statuses' => [
        'active' => 'Aktiivinen',
        'inactive' => 'Ei käytössä',
        'expired' => 'Vanhentunut',
        'pending' => 'Odottaa',
    ],
    'payment_method_added' => 'Maksutapa on lisätty.',
    'payment_method_add_failed' => 'Maksutavan lisääminen epäonnistui. Yritä uudelleen.',
    'services_linked' => ':count palvelu(a) yhdistetty',
    'remove' => 'Poista',
    'remove_payment_method' => 'Poista maksutapa',
    'remove_payment_method_confirm' => 'Oletko varma, että haluat poistaa :name? Tätä toimintoa ei voi peruuttaa.',
    'expires' => 'Vanhenee :date',
    'cancel' => 'Peruuta',
    'confirm' => 'Kyllä, poista',
    'email_notifications' => 'Sähköposti-ilmoitukset',
    'in_app_notifications' => 'Sovelluksensisäiset ilmoitukset',
    'notifications_description' => 'Hallitse ilmoitusasetuksiasi. Voit halutessasi vastaanottaa ilmoituksia sähköpostitse, sovelluksessa (push) tai molemmilla.',
    'notification' => 'Ilmoitus',

    'push_notifications' => 'Push-ilmoitukset',
    'push_notifications_description' => 'Ota käyttöön push-ilmoitukset saadaksesi reaaliaikaisia päivityksiä suoraan selaimessasi, vaikka et olisikaan sivustolla.',
    'enable_push_notifications' => 'Ota push-ilmoitukset käyttöön',
    'push_status' => [
        'not_supported' => 'Selaimesi ei tue push-ilmoituksia.',
        'denied' => 'Push-ilmoitukset on estetty. Ota ne käyttöön selaimesi asetuksissa.',
        'subscribed' => 'Push-ilmoitukset ovat käytössä.',
    ],
];
