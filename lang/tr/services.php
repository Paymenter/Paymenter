<?php

return [
    'services' => 'Hizmetler',
    'product' => 'Ürün',
    'price' => 'Ücret',
    'status' => 'Durum',
    'name' => 'Name',
    'actions' => 'İşlemler',
    'view' => 'Görüntüle',

    'product_details' => 'Ürün Detayları',
    'billing_cycle' => 'Fatura Döngüsü',
    'cancel' => 'İptal',
    'cancellation' => ':service İptali',
    'cancel_are_you_sure' => 'Bu hizmeti iptal etmek istediğinizden emin misiniz?',
    'cancel_reason' => 'İptal Nedeni',
    'cancel_type' => 'İptal Türü',
    'cancel_immediate' => 'Derhâl iptal et',
    'cancel_end_of_period' => 'Faturalama dönemi sonunda iptal et',
    'cancel_immediate_warning' => 'Aşağıdaki düğmeye bastığınızda hizmet hemen iptal edilecek ve artık kullanamayacaksınız.',
    'cancellation_requested' => 'İptal talebi gönderildi',

    'current_plan' => 'Mevcut Paket',
    'new_plan' => 'Yeni Paket',
    'change_plan' => 'Paket Değiştir',
    'current_price' => 'Mevcut Ücret',
    'new_price' => 'Yeni Ücret',
    'upgrade' => 'Yükselt',
    'upgrade_summary' => 'Yükseltme Özeti',
    'total_today' => 'Bugün Ödenecek Tutar',
    'upgrade_service' => 'Hizmeti Yükselt',
    'upgrade_choose_product' => 'Yükseltmek istediğiniz ürünü seçin',
    'upgrade_choose_config' => 'Yükseltme için yapılandırmayı seçin',
    'next_step' => 'Sonraki Adım',

    'upgrade_pending' => 'Zaten bir yükseltme/azaltma faturası açıkken yükseltme yapamazsınız',

    'outstanding_invoice' => 'Ödenmemiş bir faturanız var.',
    'view_and_pay' => 'Görüntülemek ve ödemek için tıklayın',

    'statuses' => [
        'pending' => 'Bekliyor',
        'active' => 'Aktif',
        'cancelled' => 'İptal Edildi',
        'suspended' => 'Askıya Alındı',
        'cancellation_pending' => 'İptal Beklemede',
    ],
    'billing_cycles' => [
        'day' => 'gün|gün',
        'week' => 'hafta|hafta',
        'month' => 'ay|ay',
        'year' => 'yıl|yıl',
    ],
    'every_period' => 'Her :period :unit',
    'price_every_period' => ':price her :period :unit',
    'price_one_time' => ':price tek seferlik',
    'expires_at' => 'Sona Erme Tarihi',
    'auto_pay' => 'Auto paying using',
    'auto_pay_not_configured' => 'Not configured',

    'no_services' => 'No services found',
    'update_billing_agreement' => 'Update Billing Agreement',
    'clear_billing_agreement' => 'Clear Billing Agreement',
    'select_billing_agreement' => 'Select Billing Agreement',

    'remove_payment_method' => 'Remove Payment Method',
    'remove_payment_method_confirm' => 'Are you sure you want to remove the payment method ":name" from this service? Your service will no longer be able to auto pay its invoices.',
];
