<?php

return [
    'account' => 'Fiók',
    'personal_details' => 'Személyes adatok',
    'security' => 'Biztonság',
    'credits' => 'Kreditek',

    'change_password' => 'Jelszó módosítása',

    'two_factor_authentication' => 'Kétlépcsős azonosítás',
    'two_factor_authentication_description' => 'A kétlépcsős azonosítás engedélyezésével egy extra biztonsági védelmet adhatsz a fiókodhoz.',
    'two_factor_authentication_enabled' => 'A kétlépcsős azonosítás be van kapcsolva a fiókodhoz.',
    'two_factor_authentication_enable' => 'Kétlépcsős azonosítás engedélyezése',
    'two_factor_authentication_disable' => 'Kétlépcsős azonosítás letiltása',
    'two_factor_authentication_disable_description' => 'Biztosan kikapcsolod a kétfaktoros azonosítást? Ezzel eltávolítod a fiókodból ezt az extra biztonsági védelmet.',
    'two_factor_authentication_enable_description' => 'A kétlépcsős azonosítás engedélyezéséhez a lenti QR-kódot olvasd be egy autentikátor alkalmazással, például a Google Authenticatorral vagy az Authy-val.',
    'two_factor_authentication_qr_code' => 'Olvasd be az alábbi QR-kódot az autentikátor alkalmazásoddal:',
    'two_factor_authentication_secret' => 'Vagy írd be a következő kódot manuálisan:',

    'sessions' => 'Munkamenetek',
    'sessions_description' => 'Kezeld, és jelentkezz ki aktív munkameneteidből más böngészőkön és eszközökön.',
    'logout_sessions' => 'Ez a munkamenet kijelentkeztetése',

    'input' => [
        'current_password' => 'Jelenlegi jelszó',
        'current_password_placeholder' => 'Jelenlegi jelszavad',
        'new_password' => 'Új jelszó',
        'new_password_placeholder' => 'Új jelszavad',
        'confirm_password' => 'Jelszó megerősítése',
        'confirm_password_placeholder' => 'Erősítsd meg az új jelszavad',

        'two_factor_code' => 'Add meg a hitelesítő alkalmazás által generált kódot',
        'two_factor_code_placeholder' => 'Kétlépcsős azonosító kódod',

        'currency' => 'Pénznem',
        'amount' => 'Mennyiség',
        'payment_gateway' => 'Fizetési mód',
    ],

    'notifications' => [
        'password_changed' => 'A jelszó megváltoztatásra került.',
        'password_incorrect' => 'A jelenlegi jelszavad helytelen.',
        'two_factor_enabled' => 'Kétlépcsős azonosítás engedélyezve.',
        'two_factor_disabled' => 'Kétlépcsős azonosítás letiltva.',
        'two_factor_code_incorrect' => 'A kód helytelen.',
        'session_logged_out' => 'A munkamenet ki lett jelentkeztetve.',
    ],

    'no_credit' => 'Nincs kredited.',
    'add_credit' => 'Kredit hozzáadása',
    'credit_deposit' => 'Kredit befizetés (:currency)',

    'payment_methods' => 'Fizetési módok',
    'recent_transactions' => 'Legutóbbi tranzakciók',
    'saved_payment_methods' => 'Elmentett fizetési módok',
    'setup_payment_method' => 'Új fizetési mód hozzáadása',
    'no_saved_payment_methods' => 'Nincs elmentve fizetési mód.',
    'saved_payment_methods_description' => 'Kezeld a mentett fizetési módjaidat a gyorsabb fizetés és az automatikus fizetések beállításában.',
    'no_saved_payment_methods_description' => 'Hozzáadhatsz fizetési módot, hogy a jövőbeni fizetések gyorsabbak és egyszerűbbek legyenek, és engedélyezheted a szolgáltatásaid automatikus fizetését.',
    'add_payment_method' => 'Fizetési mód hozzáadása',
    'payment_method_statuses' => [
        'active' => 'Aktív',
        'inactive' => 'Inaktív',
        'expired' => 'Lejárt',
        'pending' => 'Függőben levő',
    ],
    'payment_method_added' => 'A fizetési mód hozzáadva.',
    'payment_method_add_failed' => 'Nem sikerült hozzáadni a fizetési módot. Kérjük, próbáld meg újra.',
    'services_linked' => 'A(z) :count szolgáltatás(ok) összekapcsolva',
    'remove' => 'Eltávolítás',
    'remove_payment_method' => 'Fizetési mód eltávolítása',
    'remove_payment_method_confirm' => 'Biztosan törölni szeretnéd a(z) :name felhasználót? Ez a művelet nem vonható vissza.',
    'expires' => 'Lejár :date',
    'cancel' => 'Mégsem',
    'confirm' => 'Igen, eltávolítom',
    'email_notifications' => 'E-mail értesítések',
    'in_app_notifications' => 'Alkalmazáson belüli értesítések',
    'notifications_description' => 'Értesítési beállítások kezelése. Beállíthatod, hogy e-mailben, alkalmazáson belül (push) vagy mindkettőn keresztül kapjál értesítéseket.',
    'notification' => 'Értesítés',

    'push_notifications' => 'Push értesítések',
    'push_notifications_description' => 'Engedélyezd a Push értesítéseket, hogy valós idejű értesítéseket kapjál közvetlenül a böngésződben, még akkor is, ha nem tartózkodsz az oldalon.',
    'enable_push_notifications' => 'Push értesítések engedélyezése',
    'push_status' => [
        'not_supported' => 'A böngésződ nem támogatja a Push értesítéseket.',
        'denied' => 'A Push értesítések le vannak tiltva. Kérjük, engedélyezd őket a böngésző beállításaiban.',
        'subscribed' => 'A Push értesítések bekapcsolva.',
    ],
];
