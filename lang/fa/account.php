<?php

return [
    'account' => 'حساب کاربری',
    'personal_details' => 'اطلاعات شخصی',
    'security' => 'امنیت',
    'credits' => 'اعتبارها',

    'change_password' => 'تغییر رمز عبور',

    'two_factor_authentication' => 'احراز هویت دو مرحله‌ای',
    'two_factor_authentication_description' => 'با فعال‌سازی احراز هویت دو مرحله‌ای، لایه‌ای اضافی از امنیت به حساب شما اضافه می‌شود.',
    'two_factor_authentication_enabled' => 'احراز هویت دو مرحله‌ای برای حساب شما فعال است.',
    'two_factor_authentication_enable' => 'فعال‌سازی احراز هویت دو مرحله‌ای',
    'two_factor_authentication_disable' => 'غیرفعال‌سازی احراز هویت دو مرحله‌ای',
    'two_factor_authentication_disable_description' => 'آیا مطمئنید می‌خواهید احراز هویت دو مرحله‌ای را غیرفعال کنید؟ این کار لایه اضافی امنیت را از حساب شما حذف می‌کند.',
    'two_factor_authentication_enable_description' => 'برای فعال‌سازی احراز هویت دو مرحله‌ای، کد QR زیر را با برنامه‌ای مانند Google Authenticator یا Authy اسکن کنید.',
    'two_factor_authentication_qr_code' => 'کد QR زیر را با برنامه احراز هویت خود اسکن کنید:',
    'two_factor_authentication_secret' => 'یا کد زیر را به‌صورت دستی وارد کنید:',

    'sessions' => 'نشست‌ها',
    'sessions_description' => 'نشست‌های فعال خود را در مرورگرها و دستگاه‌های دیگر مدیریت کرده و خارج شوید.',
    'logout_sessions' => 'خروج از این نشست',

    'input' => [
        'current_password' => 'رمز عبور فعلی',
        'current_password_placeholder' => 'رمز عبور فعلی شما',
        'new_password' => 'رمز عبور جدید',
        'new_password_placeholder' => 'رمز عبور جدید شما',
        'confirm_password' => 'تأیید رمز عبور',
        'confirm_password_placeholder' => 'رمز عبور جدید خود را تأیید کنید',

        'two_factor_code' => 'کد برنامه احراز هویت خود را وارد کنید',
        'two_factor_code_placeholder' => 'کد احراز هویت دو مرحله‌ای شما',

        'currency' => 'واحد پول',
        'amount' => 'مبلغ',
        'payment_gateway' => 'درگاه پرداخت',
    ],

    'notifications' => [
        'password_changed' => 'رمز عبور تغییر کرد.',
        'password_incorrect' => 'رمز عبور فعلی نادرست است.',
        'two_factor_enabled' => 'احراز هویت دو مرحله‌ای فعال شد.',
        'two_factor_disabled' => 'احراز هویت دو مرحله‌ای غیرفعال شد.',
        'two_factor_code_incorrect' => 'کد نادرست است.',
        'session_logged_out' => 'نشست خارج شد.',
    ],

    'no_credit' => 'شما هیچ اعتباری ندارید.',
    'add_credit' => 'افزودن اعتبار',
    'credit_deposit' => 'واریز اعتبار (:currency)',

    'payment_methods' => 'روش‌های پرداخت',
    'recent_transactions' => 'تراکنش‌های اخیر',
    'saved_payment_methods' => 'روش‌های پرداخت ذخیره‌شده',
    'setup_payment_method' => 'افزودن یک روش پرداخت جدید',
    'no_saved_payment_methods' => 'شما هیچ روش پرداخت ذخیره‌شده‌ای ندارید.',
    'saved_payment_methods_description' => 'روش‌های پرداخت ذخیره‌شده خود را برای پرداخت سریع‌تر و پرداخت‌های خودکار مدیریت کنید.',
    'no_saved_payment_methods_description' => 'می‌توانید یک روش پرداخت اضافه کنید تا پرداخت‌های آینده سریع‌تر و آسان‌تر انجام شوند و پرداخت خودکار برای خدماتتان فعال شود.',
    'add_payment_method' => 'افزودن روش پرداخت',
    'payment_method_statuses' => [
        'active' => 'فعال',
        'inactive' => 'غیرفعال',
        'expired' => 'منقضی',
        'pending' => 'در انتظار',
    ],
    'payment_method_added' => 'روش پرداخت اضافه شد.',
    'payment_method_add_failed' => 'افزودن روش پرداخت با خطا مواجه شد. لطفاً دوباره تلاش کنید.',
    'services_linked' => ':count سرویس متصل',
    'remove' => 'حذف',
    'remove_payment_method' => 'حذف روش پرداخت',
    'remove_payment_method_confirm' => 'آیا از حذف :name مطمئن هستید؟ این اقدام قابل بازگشت نیست.',
    'expires' => 'منقضی می‌شود در :date',
    'cancel' => 'انصراف',
    'confirm' => 'بله، حذف کن',
    'email_notifications' => 'اعلان‌های ایمیلی',
    'in_app_notifications' => 'اعلان‌های درون‌برنامه‌ای',
    'notifications_description' => 'تنظیمات اعلان‌های خود را مدیریت کنید. می‌توانید اعلان‌ها را از طریق ایمیل، درون‌برنامه (پوش)، یا هر دو دریافت کنید.',
    'notification' => 'اعلان',

    'push_notifications' => 'اعلان‌های پوش',
    'push_notifications_description' => 'اعلان‌های پوش را فعال کنید تا حتی زمانی که در سایت نیستید، به‌روزرسانی‌های آنی را در مرورگر خود دریافت کنید.',
    'enable_push_notifications' => 'فعال‌سازی اعلان‌های پوش',
    'push_status' => [
        'not_supported' => 'اعلان‌های پوش توسط مرورگر شما پشتیبانی نمی‌شوند.',
        'denied' => 'اعلان‌های پوش مسدود شده‌اند. لطفاً آن‌ها را در تنظیمات مرورگر خود فعال کنید.',
        'subscribed' => 'اعلان‌های پوش فعال هستند.',
    ],
];
