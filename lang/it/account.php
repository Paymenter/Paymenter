<?php

return [
    'account' => 'Profilo',
    'personal_details' => 'Dati Personali',
    'security' => 'Sicurezza',
    'credits' => 'Crediti',

    'change_password' => 'Cambia Password',

    'two_factor_authentication' => 'Autenticazione a due fattori (2FA)',
    'two_factor_authentication_description' => 'Aggiungi un ulteriore livello di sicurezza al tuo account abilitando l\'autenticazione a due fattori.',
    'two_factor_authentication_enabled' => 'L\'autenticazione a più fattori è abilitata per il tuo account.',
    'two_factor_authentication_enable' => 'Abilita autenticazione 2FA',
    'two_factor_authentication_disable' => 'Disabilita l\'autenticazione a due fattori',
    'two_factor_authentication_disable_description' => 'Sei sicuro di voler disabilitare l\'autenticazione a due fattori? Sarà rimosso un livello ulteriore di sicurezza dal tuo account.',
    'two_factor_authentication_enable_description' => 'Per abilitare l\'autenticazione a due fattori, è necessario eseguire la scansione del codice QR qui sotto con un\'app di autenticazione come Google Authenticator o Authy.',
    'two_factor_authentication_qr_code' => 'Scansiona il codice QR qui sotto con la tua app di autenticazione:',
    'two_factor_authentication_secret' => 'Oppure inserisci manualmente il seguente codice:',

    'sessions' => 'Sessioni',
    'sessions_description' => 'Gestisci e disconnetti le sessioni attive su altri browser e dispositivi.',
    'logout_sessions' => 'Disconnetti questa sessione',

    'input' => [
        'current_password' => 'Password attuale',
        'current_password_placeholder' => 'Password corrente',
        'new_password' => 'Nuova Password',
        'new_password_placeholder' => 'La tua nuova password',
        'confirm_password' => 'Conferma la password',
        'confirm_password_placeholder' => 'Conferma la tua nuova password',

        'two_factor_code' => 'Inserisci il codice dalla tua app di autenticazione',
        'two_factor_code_placeholder' => 'Il tuo codice di autenticazione a due fattori',

        'currency' => 'Valuta',
        'amount' => 'Quantità',
        'payment_gateway' => 'Piattaforma di pagamento',
    ],

    'notifications' => [
        'password_changed' => 'La password è stata modificata.',
        'password_incorrect' => 'La password attuale è sbagliata.',
        'two_factor_enabled' => 'L\'autenticazione a due fattori è stata abilitata.',
        'two_factor_disabled' => 'L\'autenticazione a due fattori è stata disabilitata.',
        'two_factor_code_incorrect' => 'Il codice non è corretto ',
        'session_logged_out' => 'Sessione disconnessa.',
    ],

    'no_credit' => 'Non hai crediti.',
    'add_credit' => 'Aggiungi accredito',
    'credit_deposit' => 'Deposito di credito (:currency)',

    'payment_methods' => 'Metodo di Pagamento',
    'recent_transactions' => 'Transazioni recenti',
    'saved_payment_methods' => 'Metodi di pagamento salvati',
    'setup_payment_method' => 'Configura un nuovo metodo di pagamento',
    'no_saved_payment_methods' => 'Non hai nessun metodo di pagamento salvato.',
    'saved_payment_methods_description' => 'Gestisci i tuoi metodi di pagamento salvati per pagamenti più veloci e automatici.',
    'no_saved_payment_methods_description' => 'Puoi aggiungere un metodo di pagamento per rendere i pagamenti futuri più veloci e più facili, e abilitare i pagamenti automatici per i tuoi servizi.',
    'add_payment_method' => 'Aggiungi un metodo di pagamento',
    'payment_method_statuses' => [
        'active' => 'Attivo',
        'inactive' => 'Inattivo',
        'expired' => 'Scaduto',
        'pending' => 'In attesa',
    ],
    'payment_method_added' => 'È stato aggiunto il metodo di pagamento.',
    'payment_method_add_failed' => 'Impossibile aggiungere il metodo di pagamento. Vi preghiamo di riprovare.',
    'services_linked' => ':count servizio(i) collegati',
    'remove' => 'Rimuovi',
    'remove_payment_method' => 'Rimuovi il metodo di pagamento',
    'remove_payment_method_confirm' => 'Sei sicuro di voler rimuovere :name? Questa azione non può essere annullata.',
    'expires' => 'Scade il :date',
    'cancel' => 'Annulla',
    'confirm' => 'Si, elimina',
    'email_notifications' => 'Notifiche email',
    'in_app_notifications' => 'Notifiche in-app',
    'notifications_description' => 'Gestisci le tue preferenze di notifica. Puoi scegliere di ricevere notifiche via email, in-app (push) o entrambi.',
    'notification' => 'Notifica',

    'push_notifications' => 'Notifiche Push',
    'push_notifications_description' => 'Abilita le notifiche push per ricevere aggiornamenti in tempo reale direttamente nel tuo browser, anche quando non sei sul sito.',
    'enable_push_notifications' => 'Abilita le notifiche push',
    'push_status' => [
        'not_supported' => 'Le notifiche push non sono supportate dal browser.',
        'denied' => 'Le notifiche push sono bloccate. Abilitale nelle impostazioni del browser.',
        'subscribed' => 'Le notifiche push sono abilitate.',
    ],
];
