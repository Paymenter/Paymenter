<?php

return [
    'account' => 'الحساب',
    'personal_details' => 'البيانات الشخصية',
    'security' => 'الأمان',
    'credits' => 'الرصيد',

    'change_password' => 'تغيير كلمة المرور',

    'two_factor_authentication' => 'المصادقة الثنائية',
    'two_factor_authentication_description' => 'أضف طبقة إضافية من الأمان إلى حسابك بتفعيل المصادقة الثنائية.',
    'two_factor_authentication_enabled' => 'تم تمكين المصادقة الثنائية لحسابك.',
    'two_factor_authentication_enable' => 'تمكين المصادقة الثنائية',
    'two_factor_authentication_disable' => 'تعطيل المصادقة الثنائية',
    'two_factor_authentication_disable_description' => 'هل أنت متأكد من أنك تريد تعطيل المصادقة الثنائية؟ سيؤدي هذا إلى إزالة طبقة الأمان الإضافية على حسابك.',
    'two_factor_authentication_enable_description' => 'لتمكين المصادقة الثنائية، تحتاج إلى مسح رمز QR أدناه باستخدام تطبيق مصادقة مثل Google Authenticator أو Authy.',
    'two_factor_authentication_qr_code' => 'أفحص رمز QR أدناه باستخدام تطبيق المصادقة الخاص بك:',
    'two_factor_authentication_secret' => 'أو أدخل الرمز التالي يدويًا:',

    'sessions' => 'الجلسات',
    'sessions_description' => 'إدارة وتسجيل الخروج من جلساتك النشطة على المتصفحات والأجهزة الأخرى.',
    'logout_sessions' => 'تسجيل خروج هذه الجلسة',

    'input' => [
        'current_password' => 'كلمة المرور الحالية',
        'current_password_placeholder' => 'كلمة مرورك الحالية',
        'new_password' => 'كلمة المرور الجديدة',
        'new_password_placeholder' => 'كلمة مرورك الجديدة',
        'confirm_password' => 'تأكيد كلمة المرور',
        'confirm_password_placeholder' => 'تأكيد كلمة مرورك الجديدة',

        'two_factor_code' => 'أدخل الرمز من تطبيق المصادقة الخاص بك',
        'two_factor_code_placeholder' => 'رمز المصادقة الثنائية الخاص بك',

        'currency' => 'العملة',
        'amount' => 'المبلغ',
        'payment_gateway' => 'بوابة الدفع',
    ],

    'notifications' => [
        'password_changed' => 'تم تغيير كلمة المرور.',
        'password_incorrect' => 'كلمة المرور الحالية غير صحيحة.',
        'two_factor_enabled' => 'تم تمكين المصادقة الثنائية.',
        'two_factor_disabled' => 'تم تعطيل المصادقة الثنائية.',
        'two_factor_code_incorrect' => 'الرمز غير صحيح.',
        'session_logged_out' => 'تم تسجيل خروج الجلسة.',
    ],

    'no_credit' => 'ليس لديك أي رصيد.',
    'add_credit' => 'إضافة رصيد',
    'credit_deposit' => 'إيداع رصيد (:currency)',

    'payment_methods' => 'طرق الدفع',
    'recent_transactions' => 'المعاملات الأخيرة',
    'saved_payment_methods' => 'طرق الدفع المحفوظة',
    'setup_payment_method' => 'إعداد طريقة دفع جديدة',
    'no_saved_payment_methods' => 'ليس لديك طرق دفع محفوظة.',
    'saved_payment_methods_description' => 'إدارة طرق الدفع المحفوظة الخاصة بك من أجل الدفع السريع والدفع التلقائي.',
    'no_saved_payment_methods_description' => 'يمكنك إضافة طريقة دفع لجعل الدفعات المستقبلية أسرع وأسهل، وتمكين الدفعات التلقائية لخدماتك.',
    'add_payment_method' => 'إضافة طريقة دفع',
    'payment_method_statuses' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'expired' => 'منتهية',
        'pending' => 'في الانتظار',
    ],
    'payment_method_added' => 'تمت إضافة طريقة الدفع.',
    'payment_method_add_failed' => 'فشل في إضافة طريقة الدفع. يرجى المحاولة مرة أخرى.',
    'services_linked' => ':count الخدمات المرتبطة',
    'remove' => 'إزالة',
    'remove_payment_method' => 'إزالة طريقة الدفع',
    'remove_payment_method_confirm' => 'هل أنت متأكد من أنك تريد إزالة :name؟ لا يمكن التراجع عن هذا الإجراء.',
    'expires' => 'Expires :date',
    'cancel' => 'إلغاء',
    'confirm' => 'نعم، أزل',
    'email_notifications' => 'إشعارات البريد الإلكتروني',
    'in_app_notifications' => 'الإشعارات داخل التطبيق',
    'notifications_description' => 'إدارة تفضيلات الإشعارات الخاصة بك. يمكنك اختيار تلقي الإشعارات عبر البريد الإلكتروني، داخل التطبيق، أو كليهما.',
    'notification' => 'إشعار',

    'push_notifications' => 'الإشعارات داخل التطبيق',
    'push_notifications_description' => 'فعل الإشعارات لتلقي التحديثات مباشرة في المتصفح الخاص بك، حتى عندما لا تكون على الموقع.',
    'enable_push_notifications' => 'تفعيل الإشعارات',
    'push_status' => [
        'not_supported' => 'الإشعارات غير مدعومة من المتصفح الخاص بك.',
        'denied' => 'الإشعارات محظورة. الرجاء تمكينها في إعدادات المتصفح الخاص بك.',
        'subscribed' => 'تم تمكين الإشعارات.',
    ],
];
