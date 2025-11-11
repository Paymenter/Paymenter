<?php

return [
    'services' => '服務',
    'product' => '產品',
    'price' => '價格',
    'status' => '狀態',
    'name' => '名稱',
    'actions' => '操作',
    'view' => '檢視',

    'product_details' => '產品詳情',
    'billing_cycle' => '計費週期',
    'cancel' => '取消',
    'cancellation' => ':service 的取消',
    'cancel_are_you_sure' => '您確定要取消此服務嗎？',
    'cancel_reason' => '取消原因',
    'cancel_type' => '取消類型',
    'cancel_immediate' => '立即取消',
    'cancel_end_of_period' => '在計費週期結束時取消',
    'cancel_immediate_warning' => '當您按下下方的按鈕後，服務將立即取消，您將無法再使用。',
    'cancellation_requested' => '已請求取消',

    'current_plan' => '目前方案',
    'new_plan' => '新方案',
    'change_plan' => '變更方案',
    'current_price' => '目前價格',
    'new_price' => '新價格',
    'upgrade' => '升級',
    'upgrade_summary' => '升級摘要',
    'total_today' => '今日總計',
    'upgrade_service' => '升級服務',
    'upgrade_choose_product' => '選取要升級的產品',
    'upgrade_choose_config' => '選擇要升級的配置',
    'next_step' => '下一步',

    'upgrade_pending' => '如果已經有升級/降級的帳單則無法執行',

    'outstanding_invoice' => '您有一項未支付得款項。',
    'view_and_pay' => '點擊查看及支付',

    'statuses' => [
        'pending' => '等待中',
        'active' => '已啟用',
        'cancelled' => '已取消',
        'suspended' => '已停權',
        'cancellation_pending' => 'Cancellation Pending',
    ],
    'billing_cycles' => [
        'day' => '天',
        'week' => '週',
        'month' => '月',
        'year' => '年',
    ],
    'every_period' => '每 :period :unit',
    'price_every_period' => '每 :period :unit :price 元',
    'price_one_time' => ':price one time',
    'expires_at' => '有效期限至',
    'auto_pay' => 'Auto paying using',
    'auto_pay_not_configured' => 'Not configured',

    'no_services' => 'No services found',
    'update_billing_agreement' => 'Update Billing Agreement',
    'clear_billing_agreement' => 'Clear Billing Agreement',
    'select_billing_agreement' => 'Select Billing Agreement',

    'remove_payment_method' => 'Remove Payment Method',
    'remove_payment_method_confirm' => 'Are you sure you want to remove the payment method ":name" from this service? Your service will no longer be able to auto pay its invoices.',
];
