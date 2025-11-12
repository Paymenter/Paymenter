<?php

return [
    'account' => 'Akun',
    'personal_details' => 'Data pribadi',
    'security' => 'Keamanan',
    'credits' => 'Kredit',

    'change_password' => 'Ubah kata sandi',

    'two_factor_authentication' => 'Autentikasi dua faktor',
    'two_factor_authentication_description' => 'Tambahkan lapisan keamanan ekstra ke akun Anda dengan mengaktifkan autentikasi dua faktor.',
    'two_factor_authentication_enabled' => 'Autentikasi dua faktor diaktifkan untuk akun Anda.',
    'two_factor_authentication_enable' => 'Aktifkan autentikasi dua faktor',
    'two_factor_authentication_disable' => 'Nonaktifkan autentikasi dua faktor',
    'two_factor_authentication_disable_description' => 'Apakah Anda yakin ingin menonaktifkan autentikasi dua faktor? Ini akan menghapus lapisan keamanan tambahan dari akun Anda.',
    'two_factor_authentication_enable_description' => 'Untuk mengaktifkan autentikasi dua faktor, Anda perlu memindai kode QR di bawah ini menggunakan aplikasi autentikator seperti Google Authenticator atau Authy.',
    'two_factor_authentication_qr_code' => 'Pindah kode QR di bawah ini menggunakan aplikasi autentikator anda:',
    'two_factor_authentication_secret' => 'Atau masukkan kode berikut secara manual:',

    'sessions' => 'Sesi',
    'sessions_description' => 'Kelola dan keluar dari sesi aktif Anda pada browser dan perangkat lain.',
    'logout_sessions' => 'Keluar dari sesi ini',

    'input' => [
        'current_password' => 'Kata sandi saat ini',
        'current_password_placeholder' => 'Kata sandi Anda saat ini',
        'new_password' => 'Kata sandi baru',
        'new_password_placeholder' => 'Kata sandi baru Anda',
        'confirm_password' => 'Konfirmasi kata sandi',
        'confirm_password_placeholder' => 'Konfirmasi kata sandi baru Anda',

        'two_factor_code' => 'Masukkan kode dari aplikasi autentikator Anda',
        'two_factor_code_placeholder' => 'Kode autentikasi dua faktor Anda',

        'currency' => 'Mata Uang',
        'amount' => 'Jumlah',
        'payment_gateway' => 'Gerbang pembayaran',
    ],

    'notifications' => [
        'password_changed' => 'Kata sandi telah diubah.',
        'password_incorrect' => 'Kata sandi saat ini salah.',
        'two_factor_enabled' => 'Autentikasi dua langkah telah diaktifkan.',
        'two_factor_disabled' => 'Autentikasi dua langkah telah dinonaktifkan.',
        'two_factor_code_incorrect' => 'Kode yang Anda berikan salah.',
        'session_logged_out' => 'Sesi telah dikeluarkan.',
    ],

    'no_credit' => 'Anda tidak memiliki kredit.',
    'add_credit' => 'Tambahkan kredit',
    'credit_deposit' => 'Deposit kredit (:currency)',

    'payment_methods' => 'Metode Pembayaran',
    'recent_transactions' => 'Transaksi Terbaru',
    'saved_payment_methods' => 'Metode Pembayaran Tersimpan',
    'setup_payment_method' => 'Atur metode pembayaran baru',
    'no_saved_payment_methods' => 'Anda tidak memiliki metode pembayaran yang tersimpan.',
    'saved_payment_methods_description' => 'Kelola metode pembayaran tersimpan Anda untuk proses checkout lebih cepat dan pembayaran otomatis.',
    'no_saved_payment_methods_description' => 'Anda dapat menambahkan metode pembayaran untuk membuat pembayaran di masa mendatang lebih cepat dan mudah, serta mengaktifkan pembayaran otomatis untuk layanan Anda.',
    'add_payment_method' => 'Tambahkan metode pembayaran',
    'payment_method_statuses' => [
        'active' => 'Aktif',
        'inactive' => 'Nonaktif',
        'expired' => 'Kadaluarsa',
        'pending' => 'Tertunda',
    ],
    'payment_method_added' => 'Metode pembayaran telah ditambahkan.',
    'payment_method_add_failed' => 'Gagal menambahkan metode pembayaran. Silakan coba lagi.',
    'services_linked' => ':count layanan terhubung',
    'remove' => 'Hapus',
    'remove_payment_method' => 'Hilangkan metode pembayaran',
    'remove_payment_method_confirm' => 'Apakah Anda yakin ingin menghapus :name? Tindakan ini tidak bisa dibatalkan.',
    'expires' => 'Berakhir pada :date',
    'cancel' => 'Batalkan',
    'confirm' => 'Ya, Hapus',
    'email_notifications' => 'Notifikasi Email',
    'in_app_notifications' => 'Notifikasi dalam aplikasi',
    'notifications_description' => 'Kelola preferensi notifikasi Anda. Anda dapat memilih untuk menerima notifikasi melalui email, dalam aplikasi (push), atau keduanya.',
    'notification' => 'Notifikasi',

    'push_notifications' => 'Notifikasi Push',
    'push_notifications_description' => 'Aktifkan notifikasi push untuk menerima pembaruan secara real-time langsung di browser Anda, bahkan saat Anda tidak berada di situs.',
    'enable_push_notifications' => 'Aktifkan Notifikasi Push',
    'push_status' => [
        'not_supported' => 'Notifikasi push tidak didukung oleh browser Anda.',
        'denied' => 'Notifikasi push diblokir. Silakan aktifkan di pengaturan browser Anda.',
        'subscribed' => 'Notifikasi push diaktifkan.',
    ],
];
